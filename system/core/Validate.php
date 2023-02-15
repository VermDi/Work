<?php namespace core;


class Validate
{
    private $result = [];
    private $error = false;

    /**
     * Пример вызова валидации:
     *
     * $val = new Validate();
     *   if($er = $val->check($_POST, ['url' => 'required|min:3'])){ //если есть ошибка, можешм обработать или просто
     *      Errors::ajaxError($er); //передать в ответ запроса
     *   }
     *
     * Формат ответа:
     *   'ПОЛЕ1'=>['Ошибка'], 'ПОЛЕ2'=>['Ошибка2']
     *
     * Для AJAX  (Errors::ajaxError($er); //передать в ответ запроса)
     * ответ будет:
     * {error:1,
     *  msg: {
     *      'ПОЛЕ1'=>['Ошибка1', 'Ошибка2'],
     *      'ПОЛЕ2'=>['Ошибка2']]
     *        }
     * }
     *
     * Пример записи Validate->check($_POST, ['НазваниеПроверяемогоПоля'=>'required|int']);
     * Вернет или массив ошибок или false, если ошибок нет.
     *
     * @param $data - массив проверяемых данных, например POST данные
     * @param $rules - правила проверки, первый параметр название, далее правила через запятую
     *
     * @return array|bool
     */
    public function check($data, $rules)
    {
        foreach ($rules as $name => $ruleString) {
            /* $k - название поля, $v - правила */

            $rules = array_flip(explode("|", $ruleString));

            /* Первая проверка на существование поля, а потом уже остальное */

            if (isset($rules['required']) and empty($data[$name])) {
                $this->error($name, 'Поле является обязательным');
                continue;
            }

            /* Перебираем правила по порядку */

            foreach ($rules as $rule => $empty) {
                $realRuleWithParams = explode(":", $rule);
                if (method_exists($this, $realRuleWithParams[0])) {
                    /* любой метод проверка в данном классе должен принимать данные и параметры.
                       а возвращаеть или false или ошибку (string)!
                     */
                    $methodName = array_shift($realRuleWithParams);
                    if ($er = $this->{$methodName}($data[$name], $realRuleWithParams)) {
                        $this->error($name, $er);
                    }
                }
            }
        }
        return $this->result();

    }

    /**
     * Возвращает или массив ошибок, или false - что значит ошибок нет
     * @return bool|array
     */
    public function result()
    {
        return $this->error;
    }

    /**
     * Фиксация ошибки.
     *
     * @param $name
     * @param $error
     */
    public function error($name, $error)
    {
        $this->error[$name][] = $error;

    }

    /* ------------------- БЛОК ПРОВЕРОК ------------------- */
    /**
     * Проверка на минимальное значение, пример:  min:3
     * @param $data
     * @param $params
     * @return bool|string
     * @throws \Exception
     */
    private function min($data, $params)
    {
        if (!is_string($data) and !is_numeric($data)) {
            throw new \Exception('Проверка на длину, допустима только для переменных типа строка и число');
        }
        $min = $params[0];

        /* Если строка */
        if (is_string($data)) {
            if (strlen($data) < $min) {
                return 'Значение должно быть больше ' . $min . " символов";
            }
        }

        /* Если число */
        if (is_numeric($data) and $data < $min) {
            return 'Значение должно быть больше ' . $min;
        }

        return false;
    }

    /**
     * Проверяет коректность email
     *
     * @param $data
     * @return bool
     */
    private function email($data)
    {
        if (!filter_var($data, FILTER_VALIDATE_EMAIL)) {
            return "Не верный формат почты";
        } else {
            return false;

        }
    }
}