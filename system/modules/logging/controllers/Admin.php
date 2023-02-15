<?php
/**
 * Created by PhpStorm.
 * User: Семья
 * Date: 29.12.2018
 * Time: 11:47
 */

namespace modules\logging\controllers;

use core\Controller;
use core\Html;
use core\Request;
use modules\logging\models\LogModel;

class Admin extends Controller
{
    public function actionIndex($key = false)
    {
        /*
         * Получаем перечень доступных ключей.
         */
        $keys = LogModel::getKeys();
        if (!is_array($keys)) {
            $keys = [];
        }
        /*
         * Если переда конкретный ключ, то по нему получим данные.
         */
        $data = [];
        if ($key) {
            $data = LogModel::instance()->where('module_key', '=', Request::check($key));
        }
        Html::instance()
            ->setJs("/assets/vendors/datatables/js/jquery.dataTables.min.js");
        Html::instance()
            ->setCss("/assets/vendors/datatables/css/jquery.dataTables.min.css");
        Html::instance()->content = $this->render("/adminlist.php", ['keys' => $keys, 'rows' => $data]);
        Html::instance()->renderTemplate("@admin")->show();
    }

    public function actionGetList($key)
    {
        echo json_encode(['data'=>LogModel::instance()->where('module_key', '=', Request::check($key))->orderBy('date_time DESC')->getAll()]);
    }

}