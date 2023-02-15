<?php

namespace modules\faq\models;

use core\Model;

/**
 * Class mAnswers
 * @property int id -
 * @property string user_id -
 * @property string faq_questions_id -
 * @property string answer -
 * @property string status -
 * @property string best -
 * @property string date -
 */

class mAnswers extends Model
{
    public $table = 'faq_answers';


    public function factory($id = false)
    {
        if ($id == false or !$this->getOne($id)) {
            $this->id = "";
            $this->user_id = "";
            $this->faq_questions_id = "";
            $this->answer = "";
            $this->status = "";
            $this->best = "";
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

    public function getAnswers($options = false){
        $q = mAnswers::instance();
        if(!isset($options['select'])){
            $options['select'] = mAnswers::instance()->table.'.*, user.email as email, user.fio as fio, faq_questions.title as question ';
            $q->leftJoin('user' , mAnswers::instance()->table. '.user_id', 'user.id');
            $q->leftJoin('faq_questions' , mAnswers::instance()->table. '.faq_questions_id', 'faq_questions.id');
        }
        $q = $q->select($options['select']);
        if(isset($options['id'])){
            $q = $q->where(mAnswers::instance()->table.'.id','=',$options['id']);
        }
        if(isset($options['user_id'])){
            $q = $q->where(mAnswers::instance()->table.'.user_id','=',$options['user_id']);
        }
        if(isset($options['faq_questions_id'])){
            $q = $q->where(mAnswers::instance()->table.'.faq_questions_id','=',$options['faq_questions_id']);
        }
        if(isset($options['status'])){
            $q = $q->where(mAnswers::instance()->table.'.status','=',$options['status']);
        }
        if(isset($options['status'])){
            $q = $q->where(mAnswers::instance()->table.'.status','=',$options['status']);
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

    public function saveAnswers($data){
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