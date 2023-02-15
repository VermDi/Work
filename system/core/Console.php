<?php
/**
 * Create by e-Mind Studio
 * User: Евгения
 * Date: 31.07.2017
 * Time: 10:16
 */

namespace core;

class Console
{
    private $internalRoute;
    private $parameters;
    public static $module;

    public function __construct($config=false)
    {
        $this->getURI();
    }
    public static function stdin($raw = false)
    {
        return $raw ? fgets(\STDIN) : rtrim(fgets(\STDIN), PHP_EOL);
    }

    public static function stdout($string)
    {
        return fwrite(\STDOUT, $string);
    }

    public static function confirm($message, $default = false)
    {
        while (true) {
            static::stdout($message . ' (yes|no) [' . ($default ? 'yes' : 'no') . ']:');
            $input = trim(static::stdin());

            if (empty($input)) {
                return $default;
            }

            if (!strcasecmp($input, 'y') || !strcasecmp($input, 'yes')) {
                return true;
            }

            if (!strcasecmp($input, 'n') || !strcasecmp($input, 'no')) {
                return false;
            }
        }
    }

    public function run()
    {


        $segments       = explode('/', $this->internalRoute);
        $controllerName = array_shift($segments);
        App::$module    = "";

        if (empty($segments)) {
            $segments[] =  'index';
        }
        $controllerName = ucfirst($controllerName . 'Controller');
        $actionName     = 'action' . ucfirst(array_shift($segments));

        if (class_exists($controllerClass = '\\controllers\\' . $controllerName)) {
            $controllerObject = new $controllerClass();
            call_user_func_array(array($controllerObject, $actionName), $this->parameters);
        } elseif (class_exists($controllerClass = '\\modules\\' . App::$module . '\\controllers\\' . $controllerName)) {
            $controllerObject = new $controllerClass();
            call_user_func_array(array($controllerObject, $actionName), $this->parameters);
        } elseif (class_exists($controllerClass = '\\core\\' . $controllerName)) {
            $controllerObject = new $controllerClass();
            call_user_func_array(array($controllerObject, $actionName), $this->parameters);
        }

    }

    private function getURI()
    {
        if (!empty($_SERVER['argv'])) {

            $this->internalRoute = trim($_SERVER['argv'][1], '/');
            $this->parameters    = array(trim($_SERVER['argv'][2]));
        }
    }
}