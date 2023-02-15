<?php

namespace modules\faq\models;

use core\Model;

/**
 * Class mQuestions
 * @property int id -
 * @property string user_id -
 * @property string title -
 * @property string questions -
 * @property string status -
 * @property string date -
 */

class mQuestions extends Model
{
    public $table = 'faq_questions';


    public function factory($id = false)
    {
        if ($id == false or !$this->getOne($id)) {
            $this->id = "";
            $this->user_id = "";
            $this->title = "";
            $this->questions = "";
            $this->status = "";
            $this->date = "";
        }
        return $this;
    }

    public function getStatusArr(){
        $StatusArr[0] = 'Требует модерацию';
        $StatusArr[1] = 'Опубликован';
        $StatusArr[2] = 'Отклонен';
        return $StatusArr;
    }

    public function getQuestions($options = false){
        $q = mQuestions::instance();
        if(!isset($options['select'])){
            $options['select'] = mQuestions::instance()->table.'.*, user.email as email,  user.fio as fio ';
            $q->leftJoin('user' , mQuestions::instance()->table. '.user_id', 'user.id');
        }
        $q = $q->select($options['select']);
        if(isset($options['id'])){
            $q = $q->where(mQuestions::instance()->table.'.id','=',$options['id']);
        }
        if(isset($options['user_id'])){
            $q = $q->where(mQuestions::instance()->table.'.user_id','=',$options['user_id']);
        }
        if(isset($options['status'])){
            $q = $q->where(mQuestions::instance()->table.'.status','=',$options['status']);
        }
        if(isset($options['offset'])){
            $q = $q->offset($options['offset']);
        }
        if(isset($options['limit'])){
            $q = $q->limit($options['limit']);
        }
        if (isset($options['orderBy'])) {
            $q = $q->orderBy($options['orderBy']);
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

    public function saveQuestions($data){
        $id = (isset($data['id'])) ? $data['id'] : false;
        $model = $this->clear();
        $model->factory($id);
        if (!empty($model->id) and !empty($data['id'])) {
            $model->fill($data)->save();
        } else {
            $model->fill($data)->insert();
        }
        if ($model->insertId() != null) {
            $id = $model->insertId();
        }
        return $id;
    }


}