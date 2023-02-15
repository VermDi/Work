<?php
/**
 * Create by e-Mind Studio
 * User: Евгения
 * Date: 07.06.2017
 * Time: 14:31
 */

namespace modules\user\controllers;

use core\Controller;
use core\Html;
use modules\user\models\Role as RoleModel;
use modules\user\models\Permission as PermissionModel;
use modules\user\models\RolePermission;
use modules\user\models\UserRoles;

/**
 * @property-read RoleModel $role
 * @property-read PermissionModel $permission
 * @property-read RolePermission $rolePermission
 * @property-read UserRoles $userRole
 * Class Role
 * @package modules\user\controllers
 */
class Role extends Controller
{

    public $role;
    private $permission;
    private $rolePermission;
    private $userRole;

    function init()
    {
        $this->role           = new RoleModel();
        $this->permission     = new PermissionModel();
        $this->rolePermission = new RolePermission();
        $this->userRole       = new UserRoles();
    }

    public function actionIndex()
    {
        Html::instance()->setJs("/assets/modules/user/js/roleList.js");
        Html::instance()->title   = 'Список групп';
        Html::instance()->content = $this->render('role/index.php', ['roles' => $this->role->getAll()]);
        Html::instance()->renderTemplate('@admin')->show();
        exit;
    }

    public function actionForm($id = false)
    {
        if (!empty($_POST)) {
            if (!empty($_POST['permissions'])) {
                $permissions = $_POST['permissions'];
            }
            unset($_POST['permissions']);
            if ($role_id = $this->role->factory()->fill($_POST)->save()) {
                if (!is_integer($role_id) && !empty($_POST['id'])) {
                    $role_id = $_POST['id'];
                }
                if (!empty($permissions)) {
                    $this->rolePermission->delete(['role_id' => $role_id]);
                    foreach ($permissions AS $permission) {
                        $this->rolePermission->save(['role_id' => $role_id, 'permission_id' => $permission]);
                    }
                }
                header("Location: /user/role");
                exit;
            }
        } else {
            Html::instance()->setCss("/assets/vendors/select2/css/select2.css");
            Html::instance()->setJs("/assets/vendors/select2/js/select2.full.js");
            Html::instance()->setJs("/assets/modules/user/js/permission.js");
            Html::instance()->title   = 'Информация о группе';
            Html::instance()->content = $this->render('role/form.php', ['role' => $this->role->factory($id), 'permissions' => $this->permission->getAll()]);
            Html::instance()->renderTemplate('@admin')->show();

        }
        exit;
    }

    public function actionDelete($id)
    {
        if ($this->role->delete($id)) {
            $this->rolePermission->delete(['role_id' => $id]);
            $this->userRole->delete(['role_id' => $id]);
            header('Location: /user/role');
            exit;
        }
    }
}