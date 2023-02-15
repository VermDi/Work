<?php
/**
 * Created by PhpStorm.
 * User: Евгения
 * Date: 19.06.2017
 * Time: 17:04
 */

namespace modules\user\services;


use modules\user\models\Permission;
use modules\user\models\Role;
use modules\user\models\RolePermission;

/**
 * @property-read RolePermission $rolePermission
 * Class rolePermissionService
 * @package modules\admin\services
 */
class rolePermissionService
{
    private $role;
    private $permission;
    private $rolePermission;

    function init()
    {
        $this->setRole(new Role());
        $this->setRolePermission(new RolePermission());
    }

    public function getRolePermissions($role_id)
    {
        $this->setRolePermission(new RolePermission());
        $result = array();
        $query  = $this->rolePermission->select("permission_id")->where(['role_id' => $role_id])->getAll();
        if (!empty($query)) {
            foreach ($query as $res) {
                $result[] = $res->permission_id;
            }
        }

        return $result;
    }

    /**
     * @param Role $role
     */
    public function setRole(Role $role)
    {
        $this->role = $role;
    }

    /**
     * @param Permission $permission
     */
    public function setPermission(Permission $permission)
    {
        $this->permission = $permission;
    }

    /**
     * @param RolePermission $rolePermission
     */
    public function setRolePermission(RolePermission $rolePermission)
    {
        $this->rolePermission = $rolePermission;
    }

}