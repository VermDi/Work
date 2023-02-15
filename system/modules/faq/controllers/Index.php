<?php

namespace modules\faq\controllers;

use core\Controller;
use core\Html;
use core\ToolsHelper;
use modules\faq\models\mAnswers;
use modules\faq\models\mQuestions;
use modules\faq\services\sFAQ;
use modules\user\models\USER;

class Index extends Controller
{

    public function actionIndex()
    {
        header('Location: /faq/list');
    }

    public function actionList()
    {
        $data = [];
        $data['Questions'] = mQuestions::instance()->getQuestions(['orderBy'=>'id DESC','status'=>1]);

        html()->setJs('//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js');
        html()->setCss('//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css');
        Html::instance()->setJs("/assets/modules/faq/js/faq.js");


        Html::instance()->title = 'FAQ';

        Html::instance()->content = $this->render("/front/List.php", $data);
        Html::instance()->renderTemplate($this->config['MainTemplate'])->show();
    }

    public function actionPost($id)
    {
        $data = [];
        $data['Question'] = mQuestions::instance()->getQuestions(['id'=>$id,'getOne'=>true,'status'=>1]);
        if(empty($data['Question']->id)){
            exit('Вопрос не найден');
        }

        $data['Answers'] = mAnswers::instance()->getAnswers(['faq_questions_id'=>$id, 'status'=>1]);

        Html::instance()->setJs("/assets/modules/faq/js/faq.js");

        Html::instance()->title = 'FAQ';

        Html::instance()->content = $this->render("/front/Post.php", $data);
        Html::instance()->renderTemplate($this->config['MainTemplate'])->show();
    }

    public function actionAnswersave($dataPost = false){

        if(!empty($dataPost)){
            $data = $dataPost;
        } else {
            $data = $_POST;
        }

        //if(!\modules\recaptcha\widgets\wRecaptcha::checkForm()){ exit(json_encode(['error' => 1, 'data' => 'Ошибка, Recaptcha не пройдена'])); }

        if(empty($data['fio'])){
            echo json_encode(['error' => 1, 'data' => 'Введите ФИО']);
            exit();
        }
        if(empty($data['email'])){
            echo json_encode(['error' => 1, 'data' => 'Введите E-mail']);
            exit();
        }
        if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
            echo json_encode(['error' => 1, 'data' => 'E-mail невалидный']);
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

        $Questions = mQuestions::instance()->getQuestions(['id'=>$data['faq_questions_id'], 'getOne'=>true]);
        if(empty($Questions->id)){
            echo json_encode(['error' => 1, 'data' => 'Вопрос не найден']);
            exit();
        }

        $user_id=0;

        $USERcheck = USER::instance()->where('email', '=' , $data['email'])->getOne();
        if(isset($USERcheck->id)){
            $user_id = $USERcheck->id;
        } else {
            /*
             * Сохъраним пользователя, токены и т д
             */
            $UsersaveData=[];
            $UsersaveData['fio']=$data['fio'];
            $UsersaveData['email']=$data['email'];
            if (class_exists('modules\userextension\controllers\Index')) {
                $user_id = (new \modules\userextension\controllers\Index)->actionUsersave($UsersaveData);
            }

            if(empty($user_id)){
                echo json_encode(['error' => 1, 'data' => 'Ошибка создания пользователя']);
                exit();
            }
        }

        // Сохраним ответ
        $AnswersData=[];
        $AnswersData['date'] = date('Y-m-d H:i:s');
        $AnswersData['user_id'] = $user_id;
        $AnswersData['status'] = 0;
        $AnswersData['faq_questions_id'] = $data['faq_questions_id'];
        $AnswersData['answer'] = $data['answer'];
        $AnswersData['best'] = 0;

        $idAnswer = mAnswers::instance()->saveAnswers($AnswersData);

        // Отправим сообщение
        $message = '<p>Ваш ответ был отправлен на модерацию. Когда ответ будет опубликован, вам придет сообщение.</p>';
            if (class_exists('core\ToolsHelper')) {
                // отправим создателю ответа
                ToolsHelper::instance()->sendMail($data['email'], 'Ваш ответ успешно создан на сайте ' . $_SERVER['HTTP_HOST'], $message);
                // Отправим админу
                if(!empty($this->config['email'])){
                    $message = '<p><b>Требуется модерация ответа в модуле '.sFAQ::instance()->getHomeUri().'/faq/answers/list.</b></p>';
                    ToolsHelper::instance()->sendMail($this->config['email'], 'Новый ответ успешно создан на сайте ' . $_SERVER['HTTP_HOST'], $message);
                }
            }

        echo json_encode(['error' => 0, 'data' => 'Ваш ответ успешно создан. После проверки ответа модератором, вам будет отправлено уведомление на почту '.$data['email']]);
        exit();

    }

    public function actionQuestionsave($dataPost = false){

        if(!empty($dataPost)){
            $data = $dataPost;
        } else {
            $data = $_POST;
        }

        //if(!\modules\recaptcha\widgets\wRecaptcha::checkForm()){ exit(json_encode(['error' => 1, 'data' => 'Ошибка, Recaptcha не пройдена'])); }

        if(empty($data['fio'])){
            echo json_encode(['error' => 1, 'data' => 'Введите ФИО']);
            exit();
        }
        if(empty($data['email'])){
            echo json_encode(['error' => 1, 'data' => 'Введите E-mail']);
            exit();
        }
        if(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)){
            echo json_encode(['error' => 1, 'data' => 'E-mail невалидный']);
            exit();
        }
        if(empty($data['title'])){
            echo json_encode(['error' => 1, 'data' => 'Введите вопрос']);
            exit();
        }
        if(empty($data['questions'])){
            echo json_encode(['error' => 1, 'data' => 'Введите текст вопроса']);
            exit();
        }

        $user_id=0;

        $USERcheck = USER::instance()->where('email', '=' , $data['email'])->getOne();
        if(isset($USERcheck->id)){
            $user_id = $USERcheck->id;
        } else {
            /*
             * Сохъраним пользователя, токены и т д
             */
            $UsersaveData=[];
            $UsersaveData['fio']=$data['fio'];
            $UsersaveData['email']=$data['email'];
            if (class_exists('modules\userextension\controllers\Index')) {
                $user_id = (new \modules\userextension\controllers\Index)->actionUsersave($UsersaveData);
            }

            if(empty($user_id)){
                echo json_encode(['error' => 1, 'data' => 'Ошибка создания пользователя']);
                exit();
            }
        }

        // Сохраним вопрос
        $QuestionsData=[];
        $QuestionsData['date'] = date('Y-m-d H:i:s');
        $QuestionsData['user_id'] = $user_id;
        $QuestionsData['status'] = 0;
        $QuestionsData['title'] = $data['title'];
        $QuestionsData['questions'] = $data['questions'];

        $idQuestion = mQuestions::instance()->saveQuestions($QuestionsData);


        // Отправим сообщение
        $message = '<p>Ваш вопрос был отправлен на модерацию. Когда вопрос будет опубликован, вам придет сообщение.</p>';
            if (class_exists('core\ToolsHelper')) {
                // отправим создателю вопроса
                ToolsHelper::instance()->sendMail($data['email'], 'Ваш вопрос успешно создан на сайте ' . $_SERVER['HTTP_HOST'], $message);
                // Отправим админу
                if(!empty($this->config['email'])){
                    $message = '<p><b>Требуется модерация вопроса в модуле '.sFAQ::instance()->getHomeUri().'/faq/questions/list</b></p>';
                    ToolsHelper::instance()->sendMail($this->config['email'], 'Новый вопрос успешно создан на сайте ' . $_SERVER['HTTP_HOST'], $message);
                }
            }

        echo json_encode(['error' => 0, 'data' => 'Ваш вопрос успешно создан. Ожидайте уведомление на почту '.$data['email']]);
        exit();

    }

}