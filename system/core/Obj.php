<?php
/**
 * Create by e-Mind Studio
 * User: E_dulentsov
 * Date: 18.05.2017
 * Time: 9:17
 */

namespace core;
/**
 * Базовый объект системы
 * Class Obj
 * @package core
 */
class Obj
{
    protected static $instance;

    public static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->app = App::instance();
        $this->init();
    }

    protected function init()
    {

    }

}