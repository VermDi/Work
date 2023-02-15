<?php

namespace modules\feedback\models;

use core\Model;
use core\App;

class FeedbackModel extends Model {

    public static $currentTable = 'feedback_fields';
    public static $feedbackTable = 'feedback';

    public static function listFeedbacks() {
        $sql_result = App::instance()->db->from(self::$feedbackTable)
                ->leftJoin(self::$currentTable . ' ON ' . self::$feedbackTable . '.id_form = ' . self::$currentTable . '.id')
                ->select(self::$currentTable . '.name AS form_name, ' . self::$currentTable . '.fields AS form_fields,'
                        . self::$currentTable . '.id AS form_id, max(date) AS data, (SELECT count(*) FROM ' . self::$feedbackTable .
                        ' WHERE ' . self::$feedbackTable . '.id_form =' . self::$currentTable . '.id and ' . self::$feedbackTable . '.deleted=0) as count')
                ->orderBy(self::$feedbackTable . '.date')
                ->groupBy(self::$currentTable . '.name')
                ->where(self::$feedbackTable . '.deleted', 0)
                ->fetchAll();
        return $sql_result;
    }

    public static function getFeedback($id) {
        $sql_result = App::instance()->db->from(self::$feedbackTable)
                ->leftJoin(self::$currentTable . ' ON ' . self::$feedbackTable . '.id_form = ' . self::$currentTable . '.id')
                ->select(self::$currentTable . '.fields AS form_fields, ' . self::$currentTable . '.name as form_name')
                ->orderBy(self::$feedbackTable . '.id DESC')
                ->where(self::$feedbackTable . '.id_form', $id)
                ->where(self::$feedbackTable . '.deleted', 0)
                ->fetchAll();
        return $sql_result;
    }

    public static function saveForm($data) {
        $fields = [];
        foreach ($data['fields'] as $key => $value) {
            $fields[$key] = $value;
        }
        $data['fields'] = self::encodeValue($fields);
        $sql_result = App::instance()->db->insertInto(self::$currentTable, $data)->execute();
        self::showResult($sql_result, '/feedback/admin');
    }

    public static function saveFeedback($data) {
        if (!(self::checkFeedback($data))) {
            return ['status' => 'error', 'message' => 'Не заполненны обязательные поля'];
        } else {
            if (isset($data['redirect']) && !empty($data['redirect'])) {
                $path = $data['redirect'];
                unset($data['redirect']);
            }
            $id_form = $data['form_id'];
            unset($data['form_id']);
            App::instance()->db->insertInto(self::$feedbackTable, ['id_form' => $id_form,
                'info'    => self::encodeValue($data),
                'date'    => date("Y-m-d H:i:s")
            ])->execute();
            return ['status' => 'OK', 'message' => 'Спасибо! Ваше сообщение успешно отправлено!'];
        }
    }

    public static function deleteForm($id, $onlyFeedback = FALSE) {
        $sql_result = App::instance()->db->update(self::$feedbackTable, [self::$feedbackTable . '.deleted' => '1'])
                ->where(self::$feedbackTable . '.id', $id)
                ->execute();
        if ($onlyFeedback == FALSE) {
            $sql_result = App::instance()->db->update(self::$currentTable, [self::$currentTable . '.deleted' => '1'])
                    ->where(self::$currentTable . '.id', $id)
                    ->execute();
        }
        $path = $onlyFeedback ? $_SERVER["HTTP_REFERER"] : self::getModuleName() . 'admin/listforms';
        self::showResult($sql_result, $path);
    }

    public static function deleteAllFeedbacks($id) {

        $sql_result = App::instance()->db->update(self::$feedbackTable, [self::$feedbackTable . '.deleted' => '1'])
                ->where(self::$feedbackTable . '.id_form', $id)
                ->execute();
        self::showResult($sql_result, '/feedback/admin');
    }

    public static function showResult($result, $path = '/') {
        if ($result) {
            $_SESSION['answer'] = [
                'text' => 'Успешно',
                'type' => 'text-success',
            ];
        } else {
            $_SESSION['answer'] = [
                'text' => 'Ошибка',
                'type' => 'text-danger',
            ];
        }
        header('Location:' . $path);
    }

    public static function encodeValue($value) {
        return json_encode($value);
    }

    public static function getlistForms() {
        $sql_result = App::instance()->db->from(self::$currentTable)
                ->where(self::$currentTable . '.deleted', 0)
                ->fetchAll();
        return $sql_result;
    }

    public static function checkFeedback($data) {
        if (empty($data) || !isset($_SESSION['user']['login'])) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public static function getModuleName() {
        return '/' . App::instance()->getModule() . '/';
    }

}
