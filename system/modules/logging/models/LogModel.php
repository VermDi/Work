<?php

namespace modules\logging\models;

use core\Model;
use modules\user\models\USER;

/**
 * Class Logmodel
 * @property string id - Ключ
 * @property string module_key - Ключ модуля
 * @property string date_time - Время изменени
 * @property string message - Что сделали
 * @property string user_id - Кто сделал
 */
class LogModel extends Model
{
    public $table = 'logging';

    public function factory($id = false)
    {
        if ($id == false or !$this->getOne($id)) {
            $this->id = "";
            $this->module_key = "";
            $this->date_time = "";
            $this->message = "";
            $this->user_id = "";

        }
        return $this;
    }

    public function beforeInsert()
    {
        $this->user_id = USER::current()->id;
        $this->date_time = date("Y-m-d H:i:s");
    }

    /**
     * Возвращает список объектов или false
     * @return bool|false|mixed|\PDOStatement|\stdClass|string
     */
    public static function getKeys()
    {
        return self::instance()->select(['distinct(module_key) as module_key'])->getAll();
    }

}