<?php

namespace modules\faq\controllers;

use core\App;
use core\Controller;
use core\Html;
use core\ToolsHelper;
use modules\faq\models\mQuestions;
use modules\faq\services\sFAQ;
use modules\user\models\USER;

class Questions extends Controller
{

    public function actionIndex()
    {
        header('Location: /faq/questions/list');
    }

    public function actionList()
    {
        $data = [];
       // $data['status'] = $status;




        Html::instance()->setJs("/assets/vendors/datatables/js/jquery.dataTables.min.js");
        Html::instance()->setCss("/assets/vendors/datatables/css/jquery.dataTables.min.css");
        html()->setJs('//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js');
        html()->setCss('//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css');
        Html::instance()->setCss('/assets/vendors/jquery.datetimepicker/jquery.datetimepicker.css');
        Html::instance()->setJs('/assets/vendors/jquery.datetimepicker/jquery.datetimepicker.js');

        Html::instance()->setCss('/assets/vendors/toastr-master/toastr.css');
        Html::instance()->setJs('/assets/vendors/toastr-master/toastr.js');

        Html::instance()->setJs("/assets/modules/faq/js/questions.js");

        Html::instance()->content = $this->render("/questions/List.php", $data);
        Html::instance()->renderTemplate('@admin')->show();
    }

    public function actionSave()
    {
        $data = $_REQUEST;
        if(empty($data['user_id'])){
            echo json_encode(['error' => 1, 'data' => 'Введите user_id']);
            exit();
        }
        if(!isset($data['status'])){
            echo json_encode(['error' => 1, 'data' => 'Введите status']);
            exit();
        }
        if(empty($data['title'])){
            echo json_encode(['error' => 1, 'data' => 'Введите заголовок вопроса']);
            exit();
        }
        if(empty($data['questions'])){
            echo json_encode(['error' => 1, 'data' => 'Введите описание вопроса']);
            exit();
        }
        $user = USER::instance()->where('id', '=', $data['user_id'])->getOne();
        if(empty($user->id)){
            echo json_encode(['error' => 1, 'data' => 'Пользователь не найден']);
            exit();
        }

        $StatusArr = mQuestions::instance()->getStatusArr();

        // Если поменял статус отправим на почту
        if(!empty($data['id'])){
            $Question = mQuestions::instance()->getQuestions(['id'=>$data['id'], 'getOne'=>true]);
            if(!empty($Question->id)){
                if($Question->status!=$data['status']){
                    $statusText=(!empty($StatusArr[$data['status']]))?$StatusArr[$data['status']]:$data['status'];
                    $message = '<p>Вашему вопросу поставлен новый статус:'.$statusText.'.</p>';
                    if($data['status']==1){
                        $message .= '<p>Ваш вопрос доступен по ссылке <a href="'.sFAQ::instance()->getHomeUri().'/faq/post/'.$Question->id.'">'.$Question->title.'</a>.</p>';
                    }
                    if (class_exists('core\ToolsHelper')) {
                        // ОТправим создателю вопроса
                        ToolsHelper::instance()->sendMail($Question->email, 'Ваш вопрос на сайте ' . $_SERVER['HTTP_HOST'], $message);
                    }
                }
            }
        }

        $QuestionsData=[];
        if(!empty($data['id'])){
            $QuestionsData['id'] = $data['id'];
        } else {
            $QuestionsData['date'] = date('Y-m-d H:i:s');
        }

        $QuestionsData['user_id'] = $data['user_id'];
        $QuestionsData['status'] = $data['status'];
        $QuestionsData['title'] = $data['title'];
        $QuestionsData['questions'] = $data['questions'];

        $idQuestion = mQuestions::instance()->saveQuestions($QuestionsData);

        if(!empty($idQuestion)){
            $dataGetlist = $this->actionGetlist(false, $idQuestion, true);
            if(isset($dataGetlist[0])){
                echo json_encode(['error' => 0, 'data' => 'Успешно', 'jsonRow' => json_encode($dataGetlist[0])]);
            }
        }

        exit();
    }

    public function actionGetlist($status = false, $id = false, $getData = false)
    {
        /*
        * для передачи по ajax в формет json
        */
        $options=[];
        if(isset($_GET['start'])){
            $options['offset'] = $_GET['start'];
        }
        if(isset($_GET['length'])){
            $options['limit'] = $_GET['length'];
        }
        if(!empty($id)){
            $options['id'] = $id;
        }
        $options['orderBy']='status ASC';
        //$options['offset'] = 0;
        //$options['limit'] = 5;
        $res = mQuestions::instance()->getQuestions($options);

        $countAll = mQuestions::instance()->getQuestions(['getCount'=>true, 'status'=>$status]);
        $StatusArr = mQuestions::instance()->getStatusArr();

        if(!empty($res)){
            foreach ($res as $k => $v) {
                $res[$k]->user_id = $v->email;
                $res[$k]->status = (isset($StatusArr[$v->status])) ? $StatusArr[$v->status] : $v->status;
                $res[$k]->control = "
                <a class='btn btn-info btn-xs' href='/faq/answers/list/" . $v->id . "'>Ответы</a>
                <a data-title='Редактирование' href='/faq/questions/form/" . $v->id . "/ajax' class='btn btn-warning btn-xs ajax'><i class='fa fa-edit'></i></a>
                                <a data-id='" . $v->id . "' class='btn btn-danger btn-xs delProject'><i class='fa fa-trash-o'></i></a>";
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

    public function actionForm($id = false, $ajax = false)
    {
        if ($id == 0) {
            $id = false;
        }
        $data['model'] = mQuestions::instance()->factory($id);
        echo $this->render("/questions/Form.php", $data);
        die();
    }

    public function actionDel($id = false)
    {
        mQuestions::instance()->delete($id);

        echo json_encode(['error' => 0, 'data' => 'Успешно']);
        exit();
    }

}