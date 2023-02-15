<?php

namespace modules\feedback\controllers;

use core\Controller;
use core\Html;
use core\Parameters;
use modules\feedback\models\FeedbackModel;
use modules\feedback\models\mFeedback;
use modules\feedback\models\mFeedbackFields;

class Index extends Controller {

    public $html;
    public $menu = 'topmenu.php';

    function __construct() {
        $this->html = Html::instance();
        parent::__construct();
    }

    function actionIndex() {
        $this->html->title = 'Связаться с нами';
        $this->html->content = $this->render('form.php');
        $this->showTemplate('index');
    }

    function actionSend() {

        if (!empty($_POST['csrf_token']) && $this->csrfTokenMatch($_POST['csrf_token'])) {
            $info  = mFeedback::saveFeedback($_POST);
            if($info['status'] == 'OK') {

                $this->sendMailNotification($_POST['form_id']);

                if(isset($_POST['redirect'])) {
                    header('Location: '.$_POST['redirect']);
                    die();
                }
            } else {
                print_r($info);
            }
        } else {
            print_r (['status' => 'error', 'message' => 'Неверный CSRF-токен!']);
        }
    }

    function actionSendjson() {
        $data = $_POST;

        /*
         * Проверим обязательные поля
         */
        if(empty($data['form_id'])){
            echo json_encode(['error' => 1, 'data' => 'form_id пустой']);
            exit();
        }
        $FeedbackFields = mFeedbackFields::instance()->getFeedbackFields(['id'=>$data['form_id'], 'getOne'=>true]);
        if(empty($FeedbackFields->id)){
            echo json_encode(['error' => 1, 'data' => 'Форма не найдена']);
            exit();
        }
        /*
         * Проверим поля
         */
        if(!empty($FeedbackFields->fields)){
            $fields = json_decode($FeedbackFields->fields);
            foreach ($fields as $field){
                if(empty($data[$field->name_in_form])){
                    echo json_encode(['error' => 1, 'data' => 'Заполните поле: '.$field->name]);
                    exit();
                }
            }
            /*echo '<pre>';
            print_r($fields);
            echo '</pre>';*/
        }

        /*echo '<pre>';
        print_r($_POST);
        echo '</pre>';*/
        $info  = mFeedback::saveFeedback($_POST);
        if($info['status'] != 'OK') {
            echo json_encode(['error' => 1, 'data' => 'Ошибка']);
            exit();
        }

        $this->sendMailNotification($_POST['form_id']);

        echo json_encode(['error' => 0, 'data' => 'Успешно']);
        exit();
    }

    function sendMailNotification($form_id){
        /*
         * Получаем майл уведомлений для формы
         */
        $mailSend='';
        $FeedbackFields = mFeedbackFields::instance()->getFeedbackFields(['getOne'=>true, 'id'=>$form_id]);
        if(!empty($FeedbackFields->email)){
            $mailSend=$FeedbackFields->email;
        }
        /*
         * Если пустой, то смотрим общий майл
         */
        if(empty($mailSend)){
            $feedback_allmail = Parameters::get('feedback_allmail');
            if(!empty($feedback_allmail->allmail)){
                $mailSend = $feedback_allmail->allmail;
            }
        }

        /*
         * Домашняя ссылка
         */
        $HomeUri = '';
        if(isset($_SERVER['HTTP_HOST'])){
            $protokol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
            $HomeUri = $protokol.$_SERVER['HTTP_HOST'];
        }

        /*
         * Отправляем уведомление админу
         */
        if(!empty($mailSend)){
            $messengerData=[];
            $messengerData['when_date_send'] = date('Y-m-d');
            $messengerData['title'] = 'Новое сообщение от пользователя';
            $messengerData['text']='Новое сообщение от пользователя, <a href="'.$HomeUri.'/feedback/admin">прочитать</a>';
            $messengerData['when_date_send']=date('Y-m-d');
            $messengerData['id']=0;
            $messengerData['email']=$mailSend;
            (new \modules\messenger\controllers\Index)->actionSave($messengerData);
        }
    }

    function showTemplate($layout = '@admin') {
        $this->html->setTemplate($layout);
        $this->html->renderTemplate()->show();
    }

    function csrfTokenMatch($csrf_token){
        return hash_equals($csrf_token, json_decode($_SESSION['user'])->csrf_token);
    }

}
