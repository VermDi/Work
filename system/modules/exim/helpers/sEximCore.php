<?php
namespace modules\exim\helpers;

class sEximCore
{
    protected static $instance;

    public static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function getCoreVersion(){

    }


}