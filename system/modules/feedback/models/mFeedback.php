<?php
namespace modules\feedback\models;

use core\App;
use core\Model;
use modules\user\models\USER;


class mFeedback extends Model
{
    public $table = 'feedback';

    public static $currentTable = 'feedback_fields';
    public static $feedbackTable = 'feedback';

    public function factory($id = false)
    {
        if ($id == false or !$this->getOne($id)) {
            $this->id = "";
            $this->id_form = "";
            $this->email = "";
            $this->name = "";
            $this->theme = "";
            $this->deleted = "";
            $this->info = "";
            $this->date = "";
        }
        return $this;
    }

    public static function listFeedbacks() {
        $sql_result = mFeedback::instance()
            ->leftJoin(self::$currentTable, self::$feedbackTable.'.id_form', self::$currentTable . '.id')
            ->select(
                self::$feedbackTable . '.*, '.
                self::$currentTable . '.name AS form_name, '
                . self::$currentTable . '.fields AS form_fields,'
                . self::$currentTable . '.id AS form_id,'.
                ' max(date) AS data,'.
                ' (SELECT count(*) FROM ' . self::$feedbackTable . ' WHERE ' . self::$feedbackTable . '.id_form =' . self::$currentTable . '.id and ' . self::$feedbackTable . '.deleted=0) as count'
            )
            ->orderBy(self::$feedbackTable . '.date')
            ->groupBy(self::$currentTable . '.name')
            ->where(self::$feedbackTable . '.deleted', 0)
            ->getAll();
        return $sql_result;
    }

    public static function deleteFeedback($id) {
        $mFeedback = new mFeedback;
        $mFeedback->id = $id;
        $mFeedback->deleted = 1;
        $mFeedback->save();
        return $mFeedback->insertId();
    }

    public static function deleteAllFeedbacks($id) {
        $mFeedback = new mFeedback;
        $mFeedback->where('id_form', '=', $id)->update(['deleted' => 1]);

        /*$sql_result = App::instance()->db->update(self::$feedbackTable, [self::$feedbackTable . '.deleted' => '1'])
            ->where(self::$feedbackTable . '.id_form', $id)
            ->execute();
        self::showResult($sql_result, '/feedback/admin');*/
    }

    public static function saveFeedback($data) {

            if (isset($data['redirect']) && !empty($data['redirect'])) {
                $path = $data['redirect'];
                unset($data['redirect']);
            }
            $id_form = $data['form_id'];
            unset($data['form_id']);
            $mFeedback = new mFeedback;
            $mFeedback->id_form = $id_form;

            $mFeedback->email = json_decode($_SESSION['user'])->email; //new
            // ещё нужны name, theme, department_id, спросить про email

            $mFeedback->info = json_encode($data);
            $mFeedback->date = date("Y-m-d H:i:s");

            if ($mFeedback->save()) {
                return ['status' => 'OK', 'message' => 'Спасибо! Ваше сообщение успешно отправлено!'];
            } else {
                return ['status' => 'error', 'message' => 'Сообщение не отправлено!'];
            }


    }


    /**
     * Проверим обязательные поля
     * @param $data
     * @return bool
     */
    public static function checkFeedback($data) {
        if(empty($data['form_id'])){

        }
    }

    public static function getFeedbackArr($id) {
        $sql_result = mFeedback::instance()
            ->leftJoin(self::$currentTable , self::$feedbackTable . '.id_form', self::$currentTable . '.id')
            ->select(self::$feedbackTable.'.*, ' . self::$currentTable . '.fields AS form_fields, ' . self::$currentTable . '.name as form_name')
            ->orderBy(self::$feedbackTable . '.id DESC')
            ->where(self::$feedbackTable . '.id_form', $id)
            ->where(self::$feedbackTable . '.deleted', 0)
            ->getAll();
        return $sql_result;
    }

    public function getFeedback($options = false){
        $q = mFeedback::instance();
        if(!isset($options['select'])){
            $options['select'] = mFeedback::instance()->table.'.* ';
        }
        $q = $q->select($options['select']);
        if(isset($options['id'])){
            $q = $q->where(mFeedback::instance()->table.'.id','=',$options['id']);
        }

        $q = $q->orderBy('id DESC');
        if(isset($options['offset'])){
            $q = $q->offset($options['offset']);
        }
        if(isset($options['limit'])){
            $q = $q->limit($options['limit']);
        }

        if(isset($options['getCount'])){
            $q = $q->count('*');
            $q = $q->getOne();
            $countName = 'COUNT(*)';
            return $q->$countName;
        }

        if(isset($options['getOne'])){
            $q = $q->getOne();
        } else {
            $q = $q->getAll();
        }
        return $q;
    }
}