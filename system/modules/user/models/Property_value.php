<?php

namespace modules\user\models;

use core\Model;

/**
 * Class Property_value
 * @property string id -
 * @property string user_id -
 * @property string property_id -
 * @property string value -
 * @property string date_create -
 */
class Property_value extends Model
{
    public $table = 'user_property_values';

    public function factory($id = false)
    {
        if ($id == false or !$this->getOne($id)) {
            $this->id          = "";
            $this->user_id     = "";
            $this->property_id = "";
            $this->value       = "";
            $this->date_create = "";

        }
        return $this;
    }
}