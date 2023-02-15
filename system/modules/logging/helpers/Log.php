<?php
/**
 * Created by PhpStorm.
 * User: Семья
 * Date: 29.12.2018
 * Time: 11:15
 */

namespace modules\logging\helpers;

use modules\logging\models\LogModel;

/**
 * Позволяет логировать изменения в базе, с целью дальнейшего анализа.
 *
 * Class Log
 * @package modules\logging\helpers
 *
 * @example Log::addLog('Крутое сообщение типа (пользователь открыл страницу а)', 'Ключ модуля (например PageController)');
 */
class Log implements \modules\logging\contracts\Log
{
    /**
     * @param $message - Сообщение
     * @param $moduleKey - Ключ модуля, не обязательно
     * @return void - ничего не возвращает
     */
    public static function add($message, $moduleKey)
    {
        $log = LogModel::instance();
        $log->message = $message;
        $log->module_key = $moduleKey;
        $log->save();
    }
}