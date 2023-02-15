<?php
/**
 * Create by e-Mind Studio
 * User: dulentcov-smishko
 * Date: 19.09.2018
 * Time: 11:14
 *
 * Пример:
 * -------------------------------------------
 * Добавляем роуты
 *
 * Routes::add(array(
 * 'testing/(:num)' => 'test/$1',
 * 'posts/(:any)'   => 'news/$1'
 * ));
 *
 * Сравниваем с ссылкой
 *
 * $origin = 'testing/1';
 * echo Routes::route($origin);
 * // -> 'test/1'
 *----------------------------------------------
 * КЛЮЧИ:
 * ':any'=> '.+'
 * (:num) => '[0-9]+'
 * (:nonum) => '[^0-9]+'
 * (:alpha) => '[A-Za-z]+'
 * (:alnum)=> '[A-Za-z0-9]+'
 * (:hex)=> '[A-Fa-f0-9]+'
 * THANKS! AND SEE https://github.com/simonhamp/routes
 */

namespace core;


class Routes
{
    protected static $allow_query = true;
    protected static $routes = array();

    /**
     * Метод добавляет роуты в стек.
     * @param $src
     * @param null $dest
     */
    public static function add($src, $dest = null)
    {
        if (is_array($src)) {
            foreach ($src as $key => $val) {
                static::$routes[$key] = $val;
            }
        } elseif ($dest) {
            static::$routes[$src] = $dest;
        }
    }

    /**
     * Метод проверяет соотвествие ссылке раннее созданным роутам
     * @param $uri
     * @return string
     */
    public static function route($uri)
    {
        $qs = '';
        if (static::$allow_query && strpos($uri, '?') !== false) {
            // Break the query string off and attach later
            $qs = '?' . parse_url($uri, PHP_URL_QUERY);
            $uri = str_replace($qs, '', $uri);
        }
        // Is there a literal match?
        if (isset(static::$routes[$uri])) {
            return static::$routes[$uri] . $qs;
        }

        foreach (static::$routes as $key => $val) {

            $key = str_replace(':any', '.+', $key);
            $key = str_replace(':num', '[0-9]+', $key);
            $key = str_replace(':nonum', '[^0-9]+', $key);
            $key = str_replace(':alpha', '[A-Za-z]+', $key);
            $key = str_replace(':alnum', '[A-Za-z0-9]+', $key);
            $key = str_replace(':hex', '[A-Fa-f0-9]+', $key);
            // Does the RegEx match?
            if (preg_match('#^' . $key . '$#', $uri)) {
                // Do we have a back-reference?
                if (strpos($val, '$') !== false && strpos($key, '(') !== false) {
                    $val = preg_replace('#^' . $key . '$#', $val, $uri);
                }
                return $val . $qs;
            }
        }
        return $uri . $qs;
    }

    public static function reverseRoute($controller, $root = "/")
    {
        $index = array_search($controller, static::$routes);

        if ($index === false) {
            return null;
        }
        return $root . static::$routes[$index];
    }
}