<?php

namespace modules\news\models;

use core\Model;

/**
 * Class Categories
 * @property string id -
 * @property string name -
 * @property string alias -
 * @property string data_create -
 * @property string deleted -
 */
class Categories extends Model
{
    public $table = 'news_categories';

    public function factory($id = false)
    {
        if ($id == false or !$this->getOne($id)) {
            $this->id = null;
            $this->name = "";
            $this->alias = "";
            $this->data_create = date("Y-m-d H:i:s", time());
            $this->deleted = 0;

        }
        return $this;
    }

    /**
     * Связь к статьям
     */
    public function articles()
    {
        return $this->hasMany(Article::instance(), 'categories_id');
    }


    /**
     * Перед сохранением мы проверяем что указан урл, если нет делаем его.
     * @return bool|void
     */
    public function beforeInsert()
    {
        /*
         * Сделаем ссылку
         */

        if (empty($this->alias) and !empty($this->name)) {
            $this->alias = \URLify::filter($this->name);
        } else {
            $this->alias = \URLify::filter($this->alias);
        }
        /*
         * Уберем начальный слэш
         */
        if (substr($this->alias, 0, 1) == "/") {
            $this->alias = substr($this->alias, 1);
        }
        /*
         * Уберем конечный слэш
         *
         */
        if (substr($this->alias, 0, -1) == "/") {
            $this->alias = substr($this->alias, 0, -1);
        }
    }
}