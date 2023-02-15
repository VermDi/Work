<?php
/**
 * Create by e-Mind Studio
 * User: Евгения
 * Date: 19.06.2017
 * Time: 12:29
 */

namespace modules\user\controllers;

use core\Html;
use core\Controller;
use modules\user\models\RolePermission;
use modules\user\models\UserPermission;

/**
 * @property-read \modules\user\models\Permission $permission
 * @property-read RolePermission $rolePermission
 * @property-read UserPermission $userPermission
 * Class Permission
 * @package modules\admin\controllers
 */
class Permission extends Controller
{
    public $permission;
    private $rolePermission;
    private $userPermission;

    public function __construct($model = null)
    {
        $this->setPermission(new \modules\user\models\Permission());
        parent::__construct($model);
    }

    public function actionIndex()
    {
        Html::instance()->setJs("/assets/vendors/js-quicksearch/jquery.quicksearch.js");
        Html::instance()->setJs("/assets/modules/user/js/permission.js");
        Html::instance()->setJs("/assets/modules/user/js/permissionList.js");
        Html::instance()->title   = 'Права';
        Html::instance()->content = $this->render('permission/index.php', ['permissions' => $this->permission->getAll()]);
        Html::instance()->renderTemplate('@admin')->show();
    }

    public function actionForm($id = false)
    {
        if (!empty($_POST)) {
            if ($this->permission->factory($id)->fill($_POST)->save()) {
                header("Location: /user/permission");
                exit();
            }
        } else {
            Html::instance()->title   = 'Добавить право';
            Html::instance()->content = $this->render('permission/form.php', ['permission' => $this->permission->factory($id)]);
            Html::instance()->renderTemplate('@admin')->show();
        }
    }

    public function getScan()
    {
        sleep(1);
        $permissions = $this->permission->getAll();
        $arr         = [];
        if(!empty($permissions)){
            foreach ($permissions as $item) {
                $arr[] = $item->name;
            }
        }

        $result  = [];
        $modPath = _MOD_PATH_ . DIRECTORY_SEPARATOR;
        foreach (glob($modPath . '*', GLOB_ONLYDIR | GLOB_MARK) as $dir) {
            if (is_file($file = $dir . 'permissions.php')) {
                $result[basename($dir)] = include($file);
            }
        }
        $newResult = [];
        foreach ($result AS $key => $item) {
            if (is_array($item)) {
                $not_in_base = [];
                foreach ($item as $name => $description) {
                    if (!in_array($name, $arr)) {
                        $not_in_base[$name] = $description;

                    };
                }
                if ($not_in_base)
                    $newResult[$key] = $not_in_base;

            }
        }
        $result = $newResult;
        header('Content-type: application/json');
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        exit;
    }


    public function actionDelete($id)
    {
        $this->permission     = new \modules\user\models\Permission();
        $this->rolePermission = new RolePermission();
        $this->userPermission = new UserPermission();
        if ($this->permission->delete($id)) {
            $this->rolePermission->delete(['permission_id' => $id]);
            $this->userPermission->delete(['permission_id' => $id]);
        };
        header('Location: /user/permission');
        exit;

    }

    public function actionGetByRole()
    {

        $this->permission     = new \modules\user\models\Permission();
        $this->rolePermission = new RolePermission();
        $result               = array();
        $query                = $this->permission->select(['permission.id'])
                                                 ->leftJoin(
                                                     $this->rolePermission->table,
                                                     $this->rolePermission->table . ".permission_id",
                                                     $this->permission->table . ".id")
                                                 ->where([
                                                             $this->rolePermission->table . ".role_id" => $_POST['role_id']
                                                         ])
                                                 ->getAll();
        if (!empty($query)) {
            foreach ($query as $res) {
                $result[] = $res->id;
            }
        }
        echo json_encode($result);
        exit;

    }

    /**
     * @param \modules\user\models\Permission $permission
     */
    private function setPermission(\modules\user\models\Permission $permission)
    {
        $this->permission = $permission;
    }

}