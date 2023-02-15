<?php

namespace modules\backups\models;

use core\Model;

/**
 * Class mBackups
 * @property int id -
 * @property string comments -
 * @property string file_db -
 * @property string file_files -
 * @property string date -
 */

class mBackups extends Model
{
    public $table = 'backups';

    public function factory($id = false)
    {
        if ($id == false or !$this->getOne($id)) {
            $this->id = "";
            $this->comments = "";
            $this->file_db = "";
            $this->file_files = "";
            $this->date = "";
        }
        return $this;
    }

    public function getTypesArr(){
        $TypesArr[1] = 'Пополнение';
        $TypesArr[2] = 'Вывод';
        return $TypesArr;
    }

    public function getBackups($options = false){
        $q = mBackups::instance();
        if(!isset($options['select'])){
            $options['select'] = mBackups::instance()->table.'.* ';
        }
        $q = $q->select($options['select']);
        if(isset($options['id'])){
            $q = $q->where(mBackups::instance()->table.'.id','=',$options['id']);
        }
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

    public function saveBackups($data){
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