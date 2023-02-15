<?php
/**
 * Create by e-Mind Studio
 * User: E_dulentsov
 * Date: 18.05.2017
 * Time: 14:39
 */

namespace modules\user\models;

use core\Model;
use modules\user\services\UserPermissions;
use core\App;

/**
 * @property-read UserPermissions $userPermissions
 * Class Permission
 * @package modules\user\models
 *
 * @property string id - айди
 * @property string name - название
 * @property string description - описание
 */
class Permission extends Model
{
    public $table = "permission";

    private $userPermissions;

    public function factory($id = false)
    {
        if ($id == false or !$this->where('id', '=', $id)->getOne()) {
            $this->id = "";
            $this->name = "";
            $this->description = "";
        }
        return $this;
    }

    public function getPermissionByName($name)
    {
        return $this->getOne(['name' => $name]);
    }

    public function getPermissions($id)
    {
        $permissions = array();
        if ($id && $id !== 'null') {
            $this->setUserPermissions(new UserPermissions());
            $permissions = $this->userPermissions->getUserPermissions($id);
        }
        return $permissions;
    }

    /**
     * @param UserPermissions $userPermissions
     */
    private function setUserPermissions(UserPermissions $userPermissions)
    {
        $this->userPermissions = $userPermissions;
    }

    public static function add($name, $description)
    {
        if (self::isNameExists($name)) {
            throw new \Exception ("Permission name already exists");
        }
        $role = new self;
        $role->name = $name;
        $role->description = $description;
        $role->save();
        return $role;
    }

    private static function isNameExists($value, $excludeId = false)
    {
        $self = new self();
        $query = $self->count("id", 'count_id')->where('name', $value);
        $excludeId && $query->where('id!=?', $excludeId);
        return $query->count_id;
    }

    public function addAllPermissionToUser($user_id){
        $this->query("insert ignore into user_permission (user_id,permission_id) select $user_id as user_id, id as permission_id from permission");
    }
}