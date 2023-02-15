<?php
/**
 * Created by PhpStorm.
 * User: Семья
 * Date: 29.12.2018
 * Time: 11:16
 */

namespace modules\logging\contracts;

/**
 * Interface Log
 * @package modules\logging\contracts
 */
interface Log
{
    /**
     * Т.е. ничего не возвращает.
     * @return void
     */
    public static function add($message, $moduleKey);
}