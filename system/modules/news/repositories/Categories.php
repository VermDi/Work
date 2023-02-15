<?php
/**
 * Created by PhpStorm.
 * User: dulentcov-smishko
 * Date: 04.03.2019
 * Time: 15:38
 */

namespace modules\news\repositories;

/**
 * Источнки одной правды, категории статей получаем только здесь.
 *
 * Class Categories
 * @package modules\news\repositories
 */
class Categories
{

    /**
     * Возвращает перечень активных разделов
     * @return bool|false|mixed|\stdClass
     */
    public static function getActiveCategories()
    {
        return \modules\news\models\Categories::instance()->where('deleted', '=', 0)->getAll();
    }

    /**
     * Возвращает все категории
     */
    public static function getAllCategories()
    {
        return \modules\news\models\Categories::instance()->getAll();
    }


}