<?php


namespace modules\pages\services;

use core\Parameters;
use modules\pages\models\Page;

class Pages
{
    /**
     * Возвращает перень страниц
     * @return array|object
     */
    public static function GetForList($domain = false)
    {
        $conf = Parameters::getDefault('pages');

        $model = Page::instance()
            ->clear()->orderBy('position ASC');

        if ($conf['isMultiDomains'] == false) {
            $model->select('id,menu_name as name,left_key,right_key, level, url, visible, position')->orderBy('position ASC, left_key ASC');
        } else {
            $model->select('id,menu_name as name,left_key,right_key, level, domain, url, visible, position')->orderBy('position ASC, left_key ASC');
            if ($domain != false) {
                $model->where('domain', '=', $domain);
            }
        }

        return $model->getAll();

    }

    /**
     * Возвращает перечень уникальных доменов
     * @return array|object
     */
    public static function getDomains()
    {
        return Page::instance()->clear()->select('DISTINCT (domain)')->getAll();

    }

}