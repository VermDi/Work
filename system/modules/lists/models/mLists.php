<?php

namespace modules\lists\models;

use core\Model;
use modules\lists\helpers\ModelHelper;

/**
 * Class mLists
 * @property int id -
 * @property int name -
 * @property int canonicalName -
 * @property int pid -
 * @property int key_item -
 */

class mLists extends Model
{
    public $table = 'lists';
    public $ParentsListsArr=[];

    public function factory($id = false)
    {
        if ($id == false or !$this->getOne($id)) {
            $this->id = "";
            $this->name = "";
            $this->canonicalName = "";
            $this->pid = 0;
            $this->key_item = "";
        }
        return $this;
    }

    public function getLists($options = false){
        $q = mLists::instance();
        if(!isset($options['select'])){
            $options['select'] = mLists::instance()->table.'.*,
            (SELECT COUNT(pid_lists.id) FROM lists as pid_lists WHERE pid_lists.pid = lists.id ) as count_lists';
        }
        $q = $q->select($options['select']);
        if(isset($options['WhereRaw'])){
            $q = $q->whereRaw($options['WhereRaw']);
        }
        if(isset($options['id'])){
            $q = $q->where(mLists::instance()->table.'.id','=',$options['id']);
        }
        if(isset($options['canonicalName'])){
            $q = $q->where(mLists::instance()->table.'.canonicalName','=',$options['canonicalName']);
        }
        if(isset($options['pid'])){
            $q = $q->where(mLists::instance()->table.'.pid','=',$options['pid']);
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
            $count = 0;
            if(isset($q->$countName)){
                $count = $q->$countName;
            }
            return $count;
        }
        if(isset($options['getOne'])){
            $q = $q->getOne();
        } else {
            $q = $q->getAll();
        }
        return $q;
    }

    public function saveLists($data){
        $id = (isset($data['id'])) ? $data['id'] : false;
        $model = new mLists;
        $model->factory($id)->fill($data)->save();
        if (!$id) {
            $id = $model->insertId();
        }
        return $id;
    }


    /**
     * Получим простой массив дочерних, дочерних, дочерних ит.д. элеметов по canonicalName
     * @param $canonicalName
     * @return array
     */
    public function getListsByCanonicalName($canonicalName){
        $ListsArr=[];
        $List = mLists::instance()->getLists(['getOne'=>true, 'canonicalName'=>$canonicalName]);
        if(isset($List->id)){
            $ChildrenListsArr = $this->getChildrenListsArr($List->id);
            if(is_array($ChildrenListsArr)){
                foreach ($ChildrenListsArr as $ChildrenListsArrRow){
                    $ListsArr[$ChildrenListsArrRow['key_item']]=$ChildrenListsArrRow['name'];
                }
            }
        }
        return $ListsArr;
    }

    /**
     * Получим список дочерних, дочерних, дочерних ит.д. элеметов по id родителя с сохранением вложености
     * @param $id
     * @return array
     */
    public function getChildrenListsTreeArr($id){
        $ChildrenListsArr = $this->getTreeArray($this->getChildrenListsArr($id), $id);
        return $ChildrenListsArr;
    }

    /**
     * Получим список дочерних, дочерних, дочерних ит.д. элеметов по id родителя
     * @param $id
     * @return array
     */
    public function getChildrenListsArr($id){
        $AllChildren = mLists::instance()->_pdo->query("
select * from 
(select * from lists order by pid, id) 
lists, (select @pv := '".$id."') 
initialisation where find_in_set(pid, @pv) > 0 
and @pv := concat(@pv, ',', id )
")->fetchAll();
        return $AllChildren;
    }


    /**
     * Создаем многомерный массив с сохранением вложености
     * @param $src_arr
     * @param int $parent_id
     * @param string $NameIdColumn
     * @param string $NamePidColumn
     * @param array $tree
     * @return array
     */
    function getTreeArray($src_arr, $parent_id = 0, $NameIdColumn='id', $NamePidColumn='pid', $tree = array())
    {
        foreach($src_arr as $idx => $row)
        {
            if($row[$NamePidColumn] == $parent_id)
            {
                foreach($row as $k => $v)
                    $tree[$row[$NameIdColumn]][$k] = $v;
                unset($src_arr[$idx]);
                $tree[$row[$NameIdColumn]]['children'] = $this->getTreeArray($src_arr, $row[$NameIdColumn]);
            }
        }
        ksort($tree);
        return $tree;
    }
    
    /**
     * Получаем многомерный массив родителей до указанного элемента
     * @param $id
     * @param bool $reversed
     * @return array
     */
    public function getParentsListsArr($id, $reversed = true){
        $this->setParentsListsArr($id);
        $ParentsListsArr = $this->ParentsListsArr;
        if($reversed){
            $ParentsListsArr = array_reverse($ParentsListsArr);
        }
        return $ParentsListsArr;
    }


    /**
     * @param $id
     * @return bool
     */
    public function setParentsListsArr($id){
        $Lists = mLists::instance()->getLists(['getOne'=>true, 'id'=>$id]);
        if(isset($Lists->pid)){
            $this->ParentsListsArr[]=$Lists;
            if($Lists->pid>0){
                $this->setParentsListsArr($Lists->pid);
                return true;
            }
        }
        return true;
    }

    /**
     * Устанавливаем и проверяем по уникальному ключу перед сохранением
     * @param $data
     * @return bool
     */
    public function checkLists($data){
        $model = new mLists;
        $columns = [];
        $columns['id'] ='';
        return ModelHelper::instance()->checkRow($model, $columns, $data);
    }


}