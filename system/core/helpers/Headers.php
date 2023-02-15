<?php
/**
 * Create by e-Mind Studio
 * User: dulentcov-smishko
 * Date: 15.01.2019
 * Time: 10:25
 */

namespace core\helpers;

/**
 * Класс формирует заголовки для защиты сайта. Версия 0.1
 * Class Headers
 * @package core
 */
class Headers
{
    /**
     * Отправляет заголовки в браузер
     * @example Headers::send();
     */
    public static function send()
    {
        header('X-Content-Type-Options: nosniff'); //Установите значение nosniff, чтобы запретить браузерам выполнение контента, похожего на JavaScript, для которого не установлено правильное значение типа контента. Предотвращает атаки типа смешения MIME
        header("Access-Control-Allow-Methods: GET, HEAD, POST, OPTIONS"); //допустимые методы запроса
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Allow-Headers: Content-Type, Accept, Authorization, X-Requested-With");
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
            $http = "https";
        } else {
            $http = "http";
        }
        header("Access-Control-Allow-Origin: " . $http . "://" . $_SERVER['HTTP_HOST']); //с какого урла запросы разрешены... если нужно добавляем сюда еще один заголовок!
        header("x-xss-protection:1; mode=block"); //защита для xss
        header("Strict-Transport-Security: max-age=63072001; includeSubdomains; preloa"); //загрузка только https страниц!
        header("x-frame-options: DENY"); //запрет на вставку iframe
    }
}