<?php
/**
 * Класс для работы с урлами, осуществляет разбор и преборазования
 * Class Url 
 * @author BlackFire
 * @package core
 * 
 */

namespace core;

class Url
{
    public $url;
    protected static $instance;

    function __construct()
    {
        self::$instance = self::$instance ?: $this;
        $this->url = parse_url($_SERVER['REQUEST_URI']);

    }

    /**
     * Функция разбирает строку адреса
     * @example
     * http://DOMAIN/S_1/S_2/S_3/S_4/?s=15&d=18&l=456
     *     array ()
     *        'path' => string '/S_1/S_2/S_3/S_4/'
     *        'query' =>
     *     array ()
     *          's' => string '15'
     *          'd' => string '18'
     *          'l' => string '456'
     *    'host' => string 'DOMAIN'
     *    'way' =>
     *     array ()
     *        0 => string 'S_1'
     *        1 => string 'S_2'
     *        2 => string 'S_3'
     *        3 => string 'S_4'
     *        4 => string ''
     *
     * @param $url - передаем параметры
     * @return bool
     */
    function getUrl($url = false)
    {
        if ($url == false) {
            $url = $this->url;
        } else {
            if (is_string($url)) {
                $url = parse_url($url);
            }
        }
        if (!empty($url)) {
            $url['host'] = $_SERVER['HTTP_HOST'];
            $time = explode(".", $url['host']);
            if (count($time) == 3) {
                $url['clear_host'] = $time[1];
            } else {
                $url['clear_host'] = $time[0];
            }
        }
        if (!empty($url['path']) and $url['path'] != "/") {
            $url['way'] = explode("/", $url['path']);
            array_shift($url['way']);
        }
        if (!empty($url['query'])) {
            parse_str($url['query'], $url['query']);
        }
        return $url;
    }

    /**
     * Проверяет слэш на конце, и если что делает редирект на адрес без слэша!
     */
    public function checkSlashOnEnd()
    {
        if (substr($this->url['path'], -1) == "/" and _WWW_SLASH_ == false and $this->url['path'] != "/") {
            $new_url = substr($this->url['path'], 0, -1);
            if (!empty($this->url['query'])) {
                $new_url .= "?" . $this->url['query'];
            }
            header("Location: " . $new_url);
        }
    }
}
