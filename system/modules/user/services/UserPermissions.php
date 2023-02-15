<?php
/**
 * Created by PhpStorm.
 * User: Евгения
 * Date: 07.06.2017
 * Time: 13:06
 */

namespace modules\user\services;

use core\Db;
use modules\user\models\UserRoles as UserRole;
use modules\user\models\Permission;
use modules\user\models\USER;
use modules\user\models\UserPermission;

/**
 * @property-read USER $user
 * @property-read Permission $permission
 * @property-read UserPermission $userPermission
 * @property-read UserRole $userRole
 * Class UserPermissions
 * @package modules\user\services
 */
class UserPermissions
{
    private $user;
    private $permission;
    private $userPermission;
    private $userRole;

    public function getUserPermissions($id)
    {
        $result = array();
        $this->setUser(new USER());
        $this->setPermission(new Permission());
        $this->setUserPermission(new UserPermission());
        $this->setUserRole(new UserRole());
        /*
         * Получили права юзвера , прикрепим права группы.
         */
        $query = $this->userPermission->select([$this->userPermission->table . ".*", $this->permission->table . ".name as name"])->leftJoin($this->permission->table, $this->permission->table . '.id', $this->userPermission->table . ".permission_id")->where([$this->userPermission->table . '.user_id' => $id])->getAll();
        if (!empty($query)) {
            foreach ($query AS $qv) {
                $result[] = $qv->name;
            }
        }
        /*
         * TODO исправить это порно
         */
        if($queryGroup = Db::instance()->query("select * from permission where id in (select permission_id from role_permission where role_permission.role_id in (select role_id from user_role where user_id = $id));")) {
            $r = $queryGroup->fetchAll();
            foreach ($r as $k=>$v)
            {
                $result[] = $v['name'];
            }
        }
        return $result;

    }

    public function addUserPermission($id)
    {
        $user = USER::current();
		$this->setUserPermission(new UserPermission());
        if ($user->id && !$this->userPermission->where(['user_id' => $user->id, 'permission_id' => $id])->getOne()) {
            $this->userPermission->save(['user_id' => $user->id, 'permission_id' => $id]);
        }
    }

    public function removeUserPermission($id)
    {
        $user = User::current();
        if ($user->id) {
            $this->userPermission->delete(['user_id' => $user->id, 'permission_id' => $id]);
        }
    }

    /**
     * @param UserPermission $userPermission
     */
    private function setUserPermission(UserPermission $userPermission)
    {
        $this->userPermission = $userPermission;
    }

    /**
     * @param USER $user
     */
    private function setUser(USER $user)
    {
        $this->user = $user;
    }

    /**
     * @param Permission $permission
     */
    private function setPermission(Permission $permission)
    {
        $this->permission = $permission;
    }

    /**
     * @param UserRole $userRole
     */
    private function setUserRole(UserRole $userRole)
    {
        $this->userRole = $userRole;
    }

}