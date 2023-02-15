<?php
/**
 * Created by PhpStorm.
 * User: Евгения
 * Date: 30.05.2017
 * Time: 14:17
 */

namespace modules\user\services;


use modules\user\models\USER;
use modules\user\models\UserRoles as UserRolesModel;
use modules\user\models\Role;

/**
 * @property-read USER $user
 * @property-read UserRolesModel $userRoleModel
 * @property-read Role $role
 * Class UserRoles
 * @package modules\user\services
 */
class UserRoles
{
    private $user;
    private $role;
    private $userRoleModel;

    public function getUserRoles($id)
    {
        $this->setUserRoleModel(new UserRolesModel());
        $this->setRole(new Role());
        //TODO: разобраться почему null здесь строка!
        if (empty($id) or $id == "null") {
            return $this->role;
        }
        return $this->role->clear()->leftJoin($this->userRoleModel->table, 'user_role.role_id', 'role.id')->where('user_role.user_id', $id)
                          ->select('role.id')->select('role.name')->getAll();

    }

    /**
     * Метод который принимает инстанс юзера
     * @param USER $user
     */
    private function setUser(USER $user)
    {
        $this->user = $user;
    }


    /**
     * @param mixed $role
     */
    public function setRole(Role $role)
    {
        $this->role = $role;
    }

    public function addUserRole($user_id, $role_id)
    {
        $this->setUserRoleModel(new UserRolesModel());
        if ($user_id && $user_id != 'null' && !$this->userRoleModel->getOne(['user_id' => $user_id, 'role_id' => $role_id])) {

            $this->userRoleModel->save(['user_id' => $user_id, 'role_id' => $role_id]);
        }
    }

    /**
     * @param UserRolesModel $userRoleModel
     */
    public function setUserRoleModel(UserRolesModel $userRoleModel)
    {
        $this->userRoleModel = $userRoleModel;
    }

    public function removeUserRole($role_id)
    {
        $user = USER::current();
        if ($user->id) {
            $this->userRoleModel->delete(['user_id' => $user->id, 'role_id' => $role_id]);
        }
    }

    public function clearUserRoles($user_id)
    {
        $this->setUserRoleModel(new UserRolesModel());
        return $this->userRoleModel->delete(['user_id' => $user_id]);
    }

}