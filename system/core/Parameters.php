<?php

namespace core;

/**
 * Class Parameters класс который позовляет хранить параметры автономно! Параметры привязываются к хосту и модулю
 * Что позволит легко обновлять модули, на разных сайта и окружениях.
 * @package core
 */
class Parameters
{
    /**
     * @var self::$host хранит хост после проверки
     */
    private static $host;
    /**
     * @var self класса, в целом будет отказ от него
     */
    protected static $instance;

    function __construct()
    {
        if (!file_exists(__DIR__ . "/parameters/")) {
            FS::instance()
                ->createFolder(__DIR__ . "/parameters/");
        }
        self::$host = App::$url['clear_host'];
    }

    /**
     * @deprecated весь класс перейдет на статику, и это не нужно.
     * Возвращает инстанс класса.
     * @return Parameters
     */
    public static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Установка параметров
     * @example Parameters::set([my array or object], 'module_key');
     * @param $parameters
     * @param $module
     * @return bool|int
     * @throws \Exception
     */
    public static function set($parameters, $module)
    {
        self::$host = App::$url['clear_host'];
        if (!is_object($parameters) and !is_array($parameters)) {
            throw new \Exception('Должен быть массив или объект');
        }
        if ($parameters != false) {
            if (!file_exists(__DIR__ . "/parameters/")) {
                FS::instance()
                    ->createFolder(__DIR__ . "/parameters/");
            }
            $data = json_encode($parameters);
            return file_put_contents(__DIR__ . "/parameters/" . self::$host . "_" . $module . ".txt", $data, LOCK_EX);
        }
        return false;
    }

    /**
     * Получение параметров
     * @example $params=Parameters::get('module_key');
     * @param $module
     * @param bool $defaultValue
     * @param bool $asArray вернуть как array
     * @return mixed
     */
    public static function get($module, $defaultValue = false, $asArray = false)
    {
        self::$host = App::$url['clear_host'];
        if (file_exists(__DIR__ . "/parameters/" . self::$host . "_" . $module . ".txt")) {
            $p = json_decode(file_get_contents(__DIR__ . "/parameters/" . self::$host . "_" . $module . ".txt"), $asArray);
        } else {
            $p = self::getDefault($module);
        }
        if ($p == false) {
            return $defaultValue;
        } else {
            return $p;
        }
    }


    /**
     * Записывает парамeтры модуля в файл
     * @param bool $array - массив параметров
     * @param      $module
     * @return bool|int
     * @throws \Exception
     * @deprecated будет удалено в скором времени, используй метод set
     */
    function SetParameters($array = false, $module)
    {
      return self::set($array, $module);
    }

    /**
     * Считывает параметры модуля. и Возвращает из как объект
     * @deprecated будет удалено в скором времени, используй метод get
     * @param $module
     * @return mixed
     */
    function GetParameters($module)
    {
        return self::get($module);
    }

    public static function getAsArray($module_key)
    {
        return self::get($module_key, false, true);
    }

    /**
     * Возвращает параметры в виде массива.
     * @deprecated будет удалено в скором времени, используйте getAsArray или get
     * @param $module
     * @return bool|mixed
     */
    function GetParametersAsArray($module)
    {
        return self::getAsArray($module);
    }

    /**
     * Возврщет параметры из set модуля
     * @param $module_key
     * @return bool|mixed
     */
    public static function getDefault($module_key)
    {

        if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR . $module_key . DIRECTORY_SEPARATOR . "set.php")) {
            return include(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR . $module_key . DIRECTORY_SEPARATOR . "set.php");
        } else {
            return false;
        }
    }

    /**
     * Загружает параметры из файла set.php, по сути восстанавливает базовые параметры
     * @deprecated будет удалено в скором времени
     * @param $module - название модуля
     * @return bool|mixed
     */
    function GetDefaultParameters($module)
    {
        return self::getDefault($module);
    }
}
