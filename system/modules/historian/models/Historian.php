<?php

namespace modules\historian\models;

use core\Model;
use modules\user\models\USER;

/**
 * Class Historian
 * @property string id - Первичный ключ
 * @property string row_key - ключ строки
 * @property string mod_key - ключ доступа к истории
 * @property string value - значение которые были
 * @property string create_at - дата обновления
 * @property string user_id - кто изменил
 */
class Historian extends Model
{
    public $table = 'historian';

    public function factory($id = false)
    {
        if ($id == false or !$this->getOne($id)) {
            $this->id = "";
            $this->mod_key = "";
            $this->value = "";
            $this->user_id = "";
            $this->row_key=0;

        }
        return $this;
    }

    public function beforeInsert()
    {
        $this->user_id = USER::current()->id;

    }
}