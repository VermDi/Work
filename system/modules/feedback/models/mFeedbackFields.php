<?php
namespace modules\feedback\models;

use core\Model;


class mFeedbackFields extends Model
{
    public $table = 'feedback_fields';

    public function factory($id = false)
    {
        if ($id == false or !$this->getOne($id)) {
            $this->id = "";
            $this->name = "";
            $this->fields = "";
            $this->email = "";
        }
        return $this;
    }

    public function getFeedbackFields($options = false){
        $q = mFeedbackFields::instance();
        if(!isset($options['select'])){
            $options['select'] = mFeedbackFields::instance()->table.'.* ';
        }
        $q = $q->select($options['select']);
        if(isset($options['id'])){
            $q = $q->where(mFeedbackFields::instance()->table.'.id','=',$options['id']);
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

    public function saveFeedbackFields($data){
        $id = (isset($data['id'])) ? $data['id'] : false;
        $model = new mFeedbackFields;
        $model->factory($id)->fill($data)->save();
        if (!$id) {
            $id = $model->insertId();
        }
        return $id;
    }

    public static function getForm($form_id) {
        $sql_result = mFeedbackFields::instance()->where([
            'deleted' => 0,
            'id' => $form_id
        ])->getOne();

        return $sql_result;
    }

    public static function getlistForms() {
        $sql_result = mFeedbackFields::instance()->where('deleted', '=', 0)->getAll();
        return $sql_result;
    }

    public static function deleteFeedbackFields($id) {
        $mFeedbackFields = new mFeedbackFields;
        $mFeedbackFields->id = $id;
        $mFeedbackFields->deleted = 1;
        $mFeedbackFields->save();
        return $mFeedbackFields->insertId();
    }
}