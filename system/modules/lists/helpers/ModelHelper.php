<?php
namespace modules\lists\helpers;


class ModelHelper
{

    protected static $instance;

    public static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Если уже есть строка то вернем id
     * @param $model
     * @param $columns
     * @param $data
     * @return bool
     */
    public function checkRow($model, $columns, $data){
        $NewData = [];
        foreach ($columns as $Key => $column) {
            if(isset($data[$Key])) {
                $NewData[$Key]=$data[$Key];
            }
        }
        $query = false;
        if(count($columns) == count($NewData)) {
            $query = $model->select('id');
            foreach ($NewData as $Key => $NewDataRow) {
                $query = $query->where($Key,'=',$NewDataRow);
            }
            $query = $query->limit(1)->getOne();
        }
        if(isset($query->id) && $query->id>0) {
            return $query->id;
        } else {
            return false;
        }
    }


    /**
     * Удалим ненужные строки
     * @param $model
     * @param array $NewIds
     * @param array $Parametrs
     * @return bool
     */
    public function DelOldRows($model, $NewIds = [], $Parametrs = []){

        foreach ($Parametrs as $param => $val) {
            $model = $model
                ->where($param,'=',$val);
        }
        if(is_array($NewIds) && count($NewIds)>0) {
            $model = $model
                ->notIn('id',implode(',',$NewIds));
        }
        $model = $model
            ->delete();

        return true;
    }

    public function IndexBy($Array, $column){
        if(!is_array($Array)){
            return false;
        }
        $Rows = [];
        foreach ($Array as $Row) {
            $Rows[$Row->$column] = $Row;
        }
        return $Rows;
    }

}