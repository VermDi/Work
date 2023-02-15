<?php

namespace modules\lists\controllers;

use core\Controller;
use core\Html;
use core\Tools;
use modules\lists\models\mLists;

class Lists extends Controller
{

    public function actionIndex()
    {
        header('Location: /lists/lists/list');
    }

    public function actionList($pid = 0)
    {
        $data = [];
        $data['pid'] = $pid;

        Html::instance()->setJs("/assets/vendors/select2/js/select2.js");
        Html::instance()->setCss("/assets/vendors/select2/css/select2.min.css");

        Html::instance()->setJs("/assets/vendors/datatables/js/jquery.dataTables.min.js");
        Html::instance()->setCss("/assets/vendors/datatables/css/jquery.dataTables.min.css");

        Html::instance()->setCss("/assets/vendors/toastr/dist/jquery.toast.min.css");
        Html::instance()->setJs("/assets/vendors/toastr/dist/jquery.toast.min.js");

        if($pid>0){
            Html::instance()->setJs("/assets/modules/lists/js/elements.js");
            Html::instance()->content = $this->render("/elements/List.php", $data);
        } else {
            Html::instance()->setJs("/assets/modules/lists/js/lists.js");
            Html::instance()->content = $this->render("/lists/List.php", $data);
        }

        Html::instance()->renderTemplate($this->config['main_template'])->show();
    }

    public function actionSave()
    {
        $data = $_POST;
        if(empty($data['name'])){
            echo json_encode(['error' => 1, 'data' => 'Введите Наименование']);
            exit();
        }
        if(empty($data['canonicalName'])){
            echo json_encode(['error' => 1, 'data' => 'Введите Каноническое имя']);
            exit();
        }
        if(empty($data['id'])){
            $Lists = mLists::instance()->getLists(['getOne'=>true, 'canonicalName'=>$data['canonicalName']]);
            if(isset($Lists->id)){
                echo json_encode(['error' => 1, 'data' => 'Уже есть такое Каноническое имя']);
                exit();
            }
        }

        $ListsData=[];
        if(!empty($data['id'])){
            $ListsData['id'] = $data['id'];
        }
        $ListsData['name']=$data['name'];
        $ListsData['canonicalName']=$data['canonicalName'];
        $idLists = mLists::instance()->saveLists($ListsData);

        if(!empty($idLists)){
            $dataGetlist = $this->actionGetlist(false, $idLists, true);
            if(isset($dataGetlist[0])){
                echo json_encode(['error' => 0, 'data' => 'Успешно', 'jsonRow' => json_encode($dataGetlist[0])]);
            }
        }
        exit();
    }

    public function actionGetlist($pid = false, $id = false, $getData = false)
    {
        /*
        * для передачи по ajax в формет json
        */
        $options=[];
        $optionsCount=[];
        $optionsCount['getCount']=true;

        if(isset($_GET['start'])){
            $options['offset'] = $_GET['start'];
        }
        if(isset($_GET['length'])){
            $options['limit'] = $_GET['length'];
        }
        if($pid!==false){
            $options['pid'] = $pid;
            $optionsCount['pid']=$pid;
        }
        if(!empty($id)){
            $options['id'] = $id;
        }

        $res = mLists::instance()->getLists($options);
        $countAll = mLists::instance()->getLists($optionsCount);

        if(!empty($res)){
            foreach ($res as $k => $v) {
                $res[$k]->name = '<a href="/lists/lists/list/' . $v->id .'">' . $v->name .'</a>';
                if($v->pid>0){
                    $res[$k]->control = "
                <a title='Элементы списка' href='/lists/lists/list/" . $v->id . "' class='btn btn-warning btn-xs'><i class='fa fa-bars'></i></a>
                <a data-title='Редактировать' href='/lists/elements/form/" . $v->pid . "/" . $v->id . "' class='btn btn-warning btn-xs ajax'><i class='fa fa-edit'></i></a>
                <a data-id='" . $v->id . "' class='btn btn-danger btn-xs delLists'><i class='fa fa-trash-o'></i></a>";
                } else {
                    $res[$k]->control = "
                <a title='Элементы списка' href='/lists/lists/list/" . $v->id . "' class='btn btn-warning btn-xs'><i class='fa fa-bars'></i></a>
                <a data-title='Редактировать' href='/lists/lists/form/" . $v->id . "' class='btn btn-warning btn-xs ajax'><i class='fa fa-edit'></i></a>
                <a data-id='" . $v->id . "' class='btn btn-danger btn-xs delLists'><i class='fa fa-trash-o'></i></a>";
                }
            }
        } else {
            $res = [];
        }
        /*echo '<pre>';
        print_r($res);
        echo '</pre>';
        exit;*/

        if(!empty($getData)){
            return $res;
        } else {
            echo json_encode(['data'=>$res, 'recordsTotal'=>$countAll, 'recordsFiltered'=>$countAll]);
        }
        die();
    }

    public function actionForm($id = false)
    {
        if ($id == 0) {
            $id = false;
        }
        $data['model'] = mLists::instance()->factory($id);
        echo $this->render("/lists/Form.php", $data);
        die();
    }

    public function actionDel($id = false)
    {
        /*
         * Удаляем все дочерние элементы
         */
        $ChildrenListsArr = \modules\lists\models\mLists::instance()->getChildrenListsArr($id);
        foreach ($ChildrenListsArr as $ChildrenListsArrRow){
            mLists::instance()->delete($ChildrenListsArrRow['id']);
        }
        mLists::instance()->delete($id);
        echo json_encode(['error' => 0, 'data' => 'Успешно', 'id' => $id]);
    }

}