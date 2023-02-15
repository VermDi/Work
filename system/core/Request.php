<?php
/**
 * Create by e-Mind Studio
 * User: Pash
 * Date: 11.05.2015
 * Time: 7:10
 */

namespace core;

/**
 * Класс для работы с http запросами.
 *
 * Class Request
 * @package core
 */
class Request extends Collection
{

    protected $method;
    protected $url;

    public function __construct()
    {
        $this->replace($_REQUEST);

        $this->method($_SERVER['REQUEST_METHOD']);

        $this->url(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    }

    public static function check($param, $default = false)
    {
        if (empty($param)) {
            return $default;
        }
        if (is_numeric($param)) {
            if (is_int($param)) {
                return intval($param);
            }
            if (is_float($param)) {
                return floatval($param);
            }
        } else {
            return htmlentities($param, ENT_QUOTES);
        }
    }

    /**
     * Устанавливает метод
     *
     * @param null $method
     * @return $this
     */
    public function method($method = null)
    {
        if ($method) {
            $this->method = $method;
            return $this;
        }
        return $this->method;
    }

    /**
     * Устанавливает URL
     * @param null $url
     * @return $this
     */
    public function url($url = null)
    {
        if ($url) {
            $this->url = $url;
            return $this;
        }
        return $this->url;
    }

    /**
     * Возвращает, это ajax запрос true / false
     * @return bool
     */
    public function isAjax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
}