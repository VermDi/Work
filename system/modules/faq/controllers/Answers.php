<?php

namespace modules\faq\controllers;

use core\App;
use core\Controller;
use core\Html;
use core\ToolsHelper;
use modules\faq\models\mAnswers;
use modules\faq\models\mQuestions;
use modules\faq\services\sFAQ;
use modules\user\models\USER;

class Answers extends Controller
{

    public function actionIndex()
    {
        header('Location: /faq/answers/list');
    }

    public function actionList($faq_questions_id = false)
    {
        $data = [];
        $data['faq_questions_id'] = $faq_questions_id;
        $data['Question'] = mQuestions::instance()->getQuestions(['id'=>$faq_questions_id, 'getOne'=>true]);
       // $data['status'] = $status;

        Html::instance()->setJs("/assets/vendors/datatables/js/jquery.dataTables.min.js");
        Html::instance()->setCss("/assets/vendors/datatables/css/jquery.dataTables.min.css");
        html()->setJs('//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js');
        html()->setCss('//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css');
        Html::instance()->setCss('/assets/vendors/jquery.datetimepicker/jquery.datetimepicker.css');
        Html::instance()->setJs('/assets/vendors/jquery.datetimepicker/jquery.datetimepicker.js');

        Html::instance()->setCss('/assets/vendors/toastr-master/toastr.css');
        Html::instance()->setJs('/assets/vendors/toastr-master/toastr.js');

        Html::instance()->setJs("/assets/modules/faq/js/answers.js");

        Html::instance()->content = $this->render("/answers/List.php", $data);
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
        if(empty($data['faq_questions_id'])){
            echo json_encode(['error' => 1, 'data' => 'Введите faq_questions_id']);
            exit();
        }
        if(empty($data['answer'])){
            echo json_encode(['error' => 1, 'data' => 'Введите ответ']);
            exit();
        }

        $Question = mQuestions::instance()->getQuestions(['id'=>$data['faq_questions_id'], 'getOne'=>true]);
        if(empty($Question->id)){
            echo json_encode(['error' => 1, 'data' => 'Вопрос не найден']);
            exit();
        }

        $user = USER::instance()->where('id', '=', $data['user_id'])->getOne();
        if(empty($user->id)){
            echo json_encode(['error' => 1, 'data' => 'Пользователь не найден']);
            exit();
        }

        $StatusArr = mAnswers::instance()->getStatusArr();

        // Если поменял статус отправим на почту
        if(!empty($data['id'])){
            $Answer = mAnswers::instance()->getAnswers(['id'=>$data['id'], 'getOne'=>true]);
            if(!empty($Answer->id)){
                if($Answer->status!=$data['status']){
                    $statusText=(!empty($StatusArr[$data['status']]))?$StatusArr[$data['status']]:$data['status'];
                    $message = '<p>Вашему ответу поставлен новый статус:'.$statusText.'.</p>';
                    if($data['status']==1){
                        $message .= '<p>Ваш ответ доступен по ссылке <a href="'.sFAQ::instance()->getHomeUri().'/faq/post/'.$Question->id.'">'.$Question->title.'</a>.</p>';
                    }
                    if (class_exists('core\ToolsHelper')) {
                        // Отправим создателю ответа
                        ToolsHelper::instance()->sendMail($Answer->email, 'Ваш ответ на сайте ' . $_SERVER['HTTP_HOST'], $message);

                        // Отправим создателю вопроса
                        ToolsHelper::instance()->sendMail($Question->email, 'Новый ответ на сайте ' . $_SERVER['HTTP_HOST'], $message);
                    }
                }
            }
        }

        $AnswersData=[];
        if(!empty($data['id'])){
            $AnswersData['id'] = $data['id'];
        } else {
            $AnswersData['date'] = date('Y-m-d H:i:s');
        }

        $AnswersData['user_id'] = $data['user_id'];
        $AnswersData['status'] = $data['status'];
        $AnswersData['faq_questions_id'] = $data['faq_questions_id'];
        $AnswersData['answer'] = $data['answer'];
        $AnswersData['best'] = (!empty($data['best']))?$data['best']:0;

        $idAnswer = mAnswers::instance()->saveAnswers($AnswersData);

        if(!empty($idAnswer)){
            $dataGetlist = $this->actionGetlist(false, $idAnswer, true);
            if(isset($dataGetlist[0])){
                echo json_encode(['error' => 0, 'data' => 'Успешно', 'jsonRow' => json_encode($dataGetlist[0])]);
            }
        }

        exit();
    }

    public function actionGetlist($faq_questions_id = false, $id = false, $getData = false)
    {
        /*
        * для передачи по ajax в формет json
        */
        $options=[];
        $countOptions=[];
        if(isset($_GET['start'])){
            $options['offset'] = $_GET['start'];
        }
        if(isset($_GET['length'])){
            $options['limit'] = $_GET['length'];
        }
        if(!empty($faq_questions_id)){
            $options['faq_questions_id'] = $faq_questions_id;
            $countOptions['faq_questions_id'] = $faq_questions_id;
        }
        if(!empty($id)){
            $options['id'] = $id;
        }
        $options['orderBy']='status ASC';
        //$options['offset'] = 0;
        //$options['limit'] = 5;
        $res = mAnswers::instance()->getAnswers($options);


        $countOptions['getCount']=true;
        $countAll = mAnswers::instance()->getAnswers($countOptions);
        $StatusArr = mAnswers::instance()->getStatusArr();

        if(!empty($res)){
            foreach ($res as $k => $v) {
                $res[$k]->question = $v->question;
                $res[$k]->user_id = $v->email;
                $res[$k]->status = (isset($StatusArr[$v->status])) ? $StatusArr[$v->status] : $v->status;
                $res[$k]->control = "
                <a data-title='Редактирование' href='/faq/answers/form/" . $v->id . "/ajax' class='btn btn-warning btn-xs ajax'><i class='fa fa-edit'></i></a>
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

    public function actionForm($id = false, $ajax = false, $q_id=false)
    {
        if ($id == 0) {
            $id = false;
        }
        $data['model'] = mAnswers::instance()->factory($id);
        if ($q_id!==false) {
            $data['q_id'] = $q_id;
        }
        echo $this->render("/answers/Form.php", $data);
        die();
    }

    public function actionDel($id = false)
    {
        mAnswers::instance()->delete($id);

        echo json_encode(['error' => 0, 'data' => 'Успешно']);
        exit();
    }

}