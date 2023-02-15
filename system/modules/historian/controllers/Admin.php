<?php

namespace modules\historian\controllers;

use core\Controller;
use core\Html;
use modules\historian\helpers\History;

class Admin extends Controller
{
    function __construct()
    {
        parent::__construct();
        $this->model = new \modules\historian\models\Historian();

    }

    public function actionIndex()
    {
        Html::instance()->setJs("/assets/vendors/datatables/js/jquery.dataTables.min.js");
        Html::instance()->setCss("/assets/vendors/datatables/css/jquery.dataTables.min.css");
        Html::instance()->content = $this->render("List.php");
        Html::instance()->renderTemplate("@admin")->show();

    }

    public function actionGetlist()
    {
        /*
        * для передачи по ajax в формет json
        */
        $res = $this->model->select(['id', 'mod_key', 'row_key', 'create_at', 'user_id'])->getAll();
        $new = [];
        foreach ($res as $k => $v) {
            $v->control = "<a href='/historian/admin/delete/" . $v->id . "' class='btn btn-xs btn-danger'>DEL</a>";
            $new[] = $v;
        }
        echo json_encode(['data' => $new]);
        die();
    }

    public function actionDelete($id)
    {
        if (intval($id) < 1) {
            throw new \Exception('ERROR');
        }
        $this->model->delete($id);
        header("Location: " . $_SERVER['HTTP_REFERER']);
    }


}