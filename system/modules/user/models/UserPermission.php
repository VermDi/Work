<?php
/**
 * Create by e-Mind Studio
 * User: Евгения
 * Date: 07.06.2017
 * Time: 13:13
 */

namespace modules\user\models;


use core\Model;

/**
 * Class UserPermission
 * @package modules\user\models
 *
 * @property string id - айди
 * @property string permission_id - айди права
 * @property string user_id - айди пользователя
 * @property string value - значение
 */
class UserPermission extends Model
{
    public $table = 'user_permission';

    const VALUE_EXCLUDE = 1;
    const VALUE_INCLUDE = 0;

    public function factory($id = false)
    {
        if ($id == false or !$this->where('id', '=', $id)->getOne()) {
            $this->id            = "";
            $this->permission_id = "";
            $this->user_id       = "";
            $this->value         = "";
        }
        return $this;
    }

}