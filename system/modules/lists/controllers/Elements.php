<?php

namespace modules\lists\controllers;

use core\Controller;
use core\Html;
use core\Tools;
use modules\lists\models\mLists;

class Elements extends Controller
{

    public function actionSave()
    {
        $data = $_POST;
        if(empty($data['name'])){
            echo json_encode(['error' => 1, 'data' => 'Введите Наименование']);
            exit();
        }
        if(empty($data['pid'])){
            echo json_encode(['error' => 1, 'data' => 'Введите pid']);
            exit();
        }
        if(empty($data['key_item'])){
            echo json_encode(['error' => 1, 'data' => 'Введите key_item']);
            exit();
        }

        $ListsData=[];
        if(!empty($data['id'])){
            $ListsData['id'] = $data['id'];
        }
        $ListsData['name']=$data['name'];
        $ListsData['pid']=$data['pid'];
        $ListsData['key_item']=$data['key_item'];
        $idLists = mLists::instance()->saveLists($ListsData);

        if(!empty($idLists)){
            $dataGetlist = (new Lists())->actionGetlist(false, $idLists, true);
            if(isset($dataGetlist[0])){
                echo json_encode(['error' => 0, 'data' => 'Успешно', 'jsonRow' => json_encode($dataGetlist[0])]);
            }
        }
        exit();
    }

    public function actionForm($pid, $id = false)
    {
        if ($id == 0) {
            $id = false;
        }
        $data['pid'] = $pid;
        $data['model'] = mLists::instance()->factory($id);
        echo $this->render("/elements/Form.php", $data);
        die();
    }

}