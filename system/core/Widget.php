<?php
/**
 * Create by e-Mind Studio
 * User: dulentcov-smishko
 * Date: 18.03.2019
 * Time: 9:59
 */

namespace core;


abstract class Widget implements \core\interfaces\Widget
{

    public static function show(array $p)
    {
        return "ЗДесь должен быть вывод твоего виджета, или ты забыл добавить в свой класс этот метод show :)";
    }

    public static function preview()
    {
        return "ЗДесь должен быть привеью твоего виджета, или ты забыл добавить в свой класс метод preview  :)";
    }

    public static function possibilityList()
    {
        /**
         * return array ['{widget:module.class.method.params}','{widget:module.class.method.params}']
         */
        return false;
    }


}