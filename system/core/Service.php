<?php namespace core;

class Service {
    /**
     * @var Result;
     */
    protected $result;

    public function __construct()
    {
        $this->result = Result::create();
    }

    /**
     * @return \Core\Result
     */
    public function result()
    {
        return $this->result;
    }

    /**
     * Позовляет вызвать произвольный метод, передав в него параметры и получить результат.
     * @param $name
     * @param array $args
     * @return Result
     */
    public static function call($name, array $args = [])
    {
        /**
         * @var Service $service
         */
        $service = new static;
        try {
            $method = new \ReflectionMethod($service, $name);
            if (!$method->isProtected()) throw new \ReflectionException();
            $passed = [];

            foreach($method->getParameters() as $param) {
                /* @var $param \ReflectionParameter */
                if (array_key_exists($param->getName(), $args)) {
                    $passed[] = $args[$param->getName()];
                } elseif ($param->isDefaultValueAvailable()) {
                    $passed[] = $param->getDefaultValue();
                } else {
                    throw new \Exception("Required parameter is missing: " . $param->getName());
                }
            }

            $result = call_user_func_array([$service, $name], $passed);
            $result && $service->result()->replace($result);
        } catch(\ReflectionException $e) {
            $service->result()->setStatus(Result::STATUS_ERROR);
            $service->result()->setMessage('Method call failed: ' . $e->getMessage());
        } catch (\Exception $e) {
            $service->result()->setStatus(Result::STATUS_ERROR);
            $service->result()->setMessage($e->getMessage());
        }
        return $service->result();
    }
}