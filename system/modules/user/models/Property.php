<?php

namespace modules\user\models;

use core\Model;


/**
 * @property string id -
 * @property string name -
 * @property string title -
 * @property string description -
 * @property string details -
 * @property string type -
 * @property string create_at -
 * @property string change_at -
 * @property string owner_id -
 * @property string delete -
 * @property-read Property_value $propertyValueModel
 * Class Property
 * @package modules\user\models
 */
class Property extends Model
{
    public $table = 'user_properties';
    private $propertyValueModel;

    public function factory($id = false)
    {
        if ($id == false or !$this->getOne($id)) {
            $this->id          = "";
            $this->name        = "";
            $this->title       = "";
            $this->description = "";
            $this->details     = "";
            $this->type        = "";
            // $this->create_at   = "";
            $this->change_at   = "";
            $this->owner_id    = "";
            $this->delete      = 0;

        }
        return $this;
    }

    public function getPropertiesValues($fields, $user_id)
    {
        $this->setPropertyModel(new Property_value());
        $this->select([$this->table . '.*', $this->propertyValueModel->table . ".value", $this->propertyValueModel->table . ".id as prop_id"])->leftJoin($this->propertyValueModel->table, $this->table . ".id", $this->propertyValueModel->table . '.property_id and user_id=' . $user_id)->in('name', $fields);
        if (count($fields) == 1) {
            $this->getOne();
        } else {
            $this->getAll();
        }
    }

    public function beforeInsert()
    {
        $this->create_at = 'NOW()';
        $this->change_at = date('Y-m-d H:i:s');
    }

    public function beforeUpdate()
    {
        $this->change_at = date('Y-m-d H:i:s');
    }

    /**
     * @param Property_value $propertyValueModel
     */
    private function setPropertyModel(Property_value $propertyValueModel)
    {
        $this->propertyValueModel = $propertyValueModel;
    }
}