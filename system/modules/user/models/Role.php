<?php
/**
 * Create by e-Mind Studio
 * User: E_dulentsov
 * Date: 18.05.2017
 * Time: 14:39
 */

namespace modules\user\models;

use core\Model;
use \modules\user\services\UserRoles;
use modules\user\services\rolePermissionService;

/**
 * @property-read UserRoles $userRoles
 * @property-read rolePermissionService $rolePermissionService
 * Class Role
 * @package modules\user\models
 *
 * @property string id - айди
 * @property string name - название
 * @property string description - описание
 */
class Role extends Model
{
    public $table = "role";
    const DEFAULT_ROLE = 'guest';
    public $permissions;

    private $userRoles;
    private $rolePermissionService;

    public function factory($id = false)
    {
        if ($id == false or !$this->getWithPermissions($id)) {
            $this->id          = "";
            $this->name        = "";
            $this->description = "";
            $this->permissions = array();
        }
        return $this;
    }

    public function getRoles($id)
    {
        if ($id && $id !== 'null') {
            $this->setUserRoles(new UserRoles());
            $roles     = array();
            $userRoles = $this->userRoles->getUserRoles($id);
            if (!empty($userRoles)) {
                foreach ($userRoles AS $userRole) {
                    $roles[] = $userRole->name;
                }
            } else {
                $roles[] = self::DEFAULT_ROLE;
            }
        } else {
            $roles[] = self::DEFAULT_ROLE;
        }
        return $roles;
    }

    public function getByName($name)
    {
        return $this->getOne(['name' => $name]);
    }

    /**
     * @param UserRoles $userRoles
     */
    private function setUserRoles(UserRoles $userRoles)
    {
        $this->userRoles = $userRoles;
    }

    /**
     * @param \modules\user\models\RolePermission $permissions
     */
    public function setPermissions(\modules\user\models\RolePermission $permissions)
    {
        $this->setRolePermissionService(new rolePermissionService());
        $this->permissions = ($this->id) ? ($this->rolePermissionService->getRolePermissions($this->id)) : array();
    }

    private function getWithPermissions($id)
    {
        $this->where('id', '=', $id)->getOne();
        $this->setPermissions(new \modules\user\models\RolePermission());
        return $this;
    }

    /**
     * @param rolePermissionService $rolePermissionService
     */
    private function setRolePermissionService(rolePermissionService $rolePermissionService)
    {
        $this->rolePermissionService = $rolePermissionService;
    }

    public function addRolesToUser($user_id, $roles)
    {
        $this->setUserRoles(new UserRoles());
        if (!is_array($roles)) {
            $roles[] = $roles;
        }
        foreach ($roles as $role) {
            $r = $this->getByName($role);
            if($r){
                $this->userRoles->addUserRole($user_id, $r->id);
            }

        }


    }

}