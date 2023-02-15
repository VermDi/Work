<?php namespace core;
/**
 * Класс для работы с кэшем, пока безумной простой, без заморочек.
 * Пример работы:
 *
 * Кэшируем переданные значения по ключу
 * $cache = new Cache();
 * $cache->setCache('KEY', $DATA, $STORE=null);
 *
 * Получаем кэшированные значения по ключу
 * $cache = new Cache();
 * $cache->getCache('KEY', $STORE=null);
 *
 * Если необходимо контролировать время жизни кэша, то просто добавляем при получении:
 *
 * Получаем кэшированные значения по ключу с определенным временем
 * $cache = new Cache();
 * $cache->time = 5; //указывается в секундах
 * $cache->setCache('KEY', $DATA, $STORE=null);
 *
 * Class Cache
 * @package core
 */
class Cache
{

    public $time = 18000; //время кэша по умолчанию

    /**
     * Устанавливает кэширование элемента по ключу.
     * @param $store=null - общий склад для единого кэша - необзательный параметр
     * @param $key - ключ кэша
     * @param $data - дата
     */
    public function setCache($key, $data, $store = null)
    {
        $store = ($store === null) ? "" : $store . "/";
        if (!empty($data)) {
            $k = md5($key);
            if (!file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/cache/" . $store . $k . ".ccc", serialize($data))) {
                die($_SERVER['DOCUMENT_ROOT'] . "/cache/" . $k . ".ccc");
            }
        }

    }

    /**
     * Возвращает кэш по ключу.
     * @param null $store - общий склад для единого кэша - необязательный параметр
     * @param $key - ключ кэша
     * @return bool|mixed
     */
    public function getCache($key, $store = null)
    {
        $k = md5($key);
        $store = ($store === null) ? "" : $store . "/";
        $path = $_SERVER['DOCUMENT_ROOT'] . "/cache/" . $store . $k . ".ccc";
        if (file_exists($path) and (time() - filemtime($path)) < $this->time) {
            if ($_SESSION['user']['rights'] == _ADMIN_RIGHT_) {
                return false;
            } else {
                return unserialize(file_get_contents($path));
            }
        } else {
            return false;
        }
    }


}