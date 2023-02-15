<?php
/**
 * Create by e-Mind Studio
 * User: Евгения
 * Date: 17.11.2017
 * Time: 9:31
 */

namespace modules\user\controllers;


use core\Controller;

/**
 * @property-read \modules\user\models\Permission $permission
 * Class PermissionScan
 * @package modules\user\controllers
 */
class Permissionscan extends Controller
{
    public $permission;

    public function __construct($model = null)
    {
        $this->setPermission(new \modules\user\models\Permission());
        parent::__construct($model);
    }

    public function postScan()
    {

        sleep(1);
        $permissions = $this->permission->getAll();
        $arr         = [];
        if(!empty($permissions)){
            foreach ($permissions as $item) {
                $arr[] = $item->name;
            }
        }

        $permissions = $arr;
        $result      = [];

        $modPath = _MOD_PATH_ . DIRECTORY_SEPARATOR;
        foreach (glob($modPath . '*', GLOB_ONLYDIR | GLOB_MARK) as $dir) {
            if (is_file($file = $dir . 'permissions.php')) {
                if (is_array(include($file)))
                    $result = array_merge($result, include($file));
            }
        }
        $isSuccess = true;
        $arr       = [];
        foreach ($result as $name => $description) {
            if (!in_array($name, $permissions)) {
                try {
                    \modules\user\models\Permission::add($name, $description);
                    $arr[$name] = ['type' => 'success', 'message' => 'OK'];
                } catch (\Exception $e) {
                    $isSuccess  = false;
                    $arr[$name] = ['type' => 'danger', 'message' => $e->getMessage()];
                }
            };

        }
        header('Content-type: application/json');
        echo json_encode(['data' => $arr, 'result' => $isSuccess], JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT);
        exit;
    }

    public function postAddAdmin()
    {
        $this->permission = new \modules\user\models\Permission();
        $this->permission->addAllPermissionToUser(1);

    }

    /**
     * @param \modules\user\models\Permission $permission
     */
    private function setPermission(\modules\user\models\Permission $permission)
    {
        $this->permission = $permission;
    }

}