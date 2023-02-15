<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 16.02.2019
 * Time: 12:59
 */

namespace modules\historian\helpers;


use modules\historian\models\Historian;

class History
{

    /**
     * Метод взводит запись об изменений по ключу модуля и ключу записи
     * @param string $mod_key
     * @param string $row_key
     * @param $val
     * @return bool
     * @throws \Exception
     */
    public static function set($mod_key, $row_key, $val)
    {
        $h = Historian::instance()->clear();
        $h->mod_key = $mod_key;
        $h->row_key = $row_key;
        $h->value = serialize($val);
        return $h->insert();

    }

    /**
     * Метод возвращает сохраненное значение по указанной строке
     * @param string $mod_key
     * @param int $id
     * @return bool|false|mixed|\PDOStatement|\stdClass|string
     */
    public static function get(string $mod_key, $id)
    {
        $h = Historian::instance()->clear();
        $h->where('mod_key', '=', $mod_key);

        $h->where('id', '=', $id);
        $result = $h->getOne();
        if ($result) {
            return unserialize($result->value);
        }

    }

    /**
     * Метод возварщает всю историю по ключу модуля и ключу записи,
     * например всю историю по какой то странице
     *
     * @param string $mod_key
     * @param $row_key
     * @return bool|false|mixed|\PDOStatement|\stdClass|string
     */
    public static function getHistory($mod_key, $row_key)
    {
        $h = Historian::instance();
        $h->where('mod_key', '=', $mod_key);
        $h->where('row_key', '=', $row_key);
        $h->orderBy('create_at', null, 'DESC');
        return $h->getAll();
    }

    public static function getById($id)
    {
        return Historian::instance()->getOne($id);
    }

}