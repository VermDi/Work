<?php


namespace modules\amocrm\models;


use core\App;
use core\Model;

/**
 *
 * @property integer id - айди
 * @property string name - название
 * @property string type - тип
 * @property string name_in_form - имя в форме
 * @property integer is_api_only - разрешить редактировать только из api
 * @property integer id_field - айди поля
 * @property integer category - категория
 * @property string code - уникальный код
 * Class AmoCrmFields
 * @package modules\amocrm\models
 */
class AmoCrmFields extends Model
{
    public $table = 'amocrm_fields';

    public function factory($id = false)
    {
        if ($id == false or !$this->getOne($id)) {
            $this->id = "";
            $this->name = NULL;
            $this->type = NULL;
            $this->name_in_form = NULL;
            $this->is_api_only = NULL;
            $this->id_field = NULL;
            $this->category = NULL;
            $this->code = NULL;
        }
        return $this;
    }

    public function DeleteField($field_id, $items = false)
    {
        if (is_array($field_id) && count($field_id) > 0) {
            foreach ($field_id as $key => $val) {
                $this->delete(['id_field' => $val]);// удалить
            }
        }


    }


}