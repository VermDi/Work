<?php

namespace modules\menu\models;

use core\Model;
use core\Tools;
use modules\user\models\USER;


/**
 * Class Page
 * @property string id -
 * @property string name -
 * @property string visible -
 * @property string url -
 * @property string create_at -
 * @property string update_at -
 * @property string user_id -
 * @property string level -
 * @property string left_key -
 * @property string right_key -
 * @property string domain -
 * @property string is_nofollow
 * @property string is_noindex
 * @property string position
 * @property string extData
 */
class Menu extends Model
{
    public $table = 'menu';

	public function factory($id = false)
	{
		if ($id == false or !$this->getOne($id)) {
			$this->id = "";
			$this->name = "";
			$this->visible = 1;
			$this->url = "";
			//$this->create_at = "";
			$this->update_at = "NULL";
			$this->user_id = "";
			$this->level = 0;
			$this->left_key = "";
			$this->right_key = "";
			$this->domain = "";
			$this->is_nofollow = "0";
			$this->is_noindex = "0";
			$this->position = 1;
			$this->extData = null;

        }
        return $this;
    }

    /**
     * Прежде чем вставлять  сделаем урл, и проставим ключи границ
     * @return bool
     */
    public function beforeInsert()
    {
        //Вычисляем границы если они не переданы
        if (empty($_POST['left_key'])) { //если границы переданы
            $time = new self();
            $time->clear()->select('max(right_key) as m')->getOne();
            $this->left_key  = $time->m + 1; //взводим левую границу
            $this->right_key = $time->m + 2; //взводим правую границу
            $this->level     = 0;
            $time->clear()->select('max(position) as p')->where(['level' => 0])->getOne();
            $this->position = $time->p + 1;
        }

        //время создания
        $this->create_at = date("Y-m-d H:i:s", time());

        //взводим создателя записи
        if (!empty(USER::current()->id)) {
            $this->user_id = USER::current()->id;
        } else {
            $this->user_id = 0;
        }

        //  $this->clear()->select('max(position) as m')->where()->getOne();
        //возвращаем тру есил все ок
        return true;

    }

    /**
     * Прежде чем обновлять запись проверим что url есть и проерим его
     * @return bool
     */
    public function beforeUpdate()
    {
        $this->update_at = date("Y-m-d H:i:s", time());
        return true;
    }

    /**
     * Прежде чем удадять, удалим все вложенности
     * @return bool
     */
    public function beforeDelete()
    {
        /*
         * Сносим дочерние элементы
         */
        $this->id = false; //чтобы включить массоеое удаление
        //$this->where('left_key', ">=", $this->left_key)->where('right_key', '<=', $this->right_key);
        return true;
    }


    public function GetForList()
    {
        $new_array = "";
        return $this->clear()->select('id,name, url ,left_key,right_key, level, visible, position')->orderBy('position ASC, left_key ASC')->getAll();

    }

    public function moveNode($pk, $pid)
    {
        $primary_row = $this->clear()->where(['id' => $pk])->getOne();
        $left_id       = $primary_row->left_key;
        $right_id      = $primary_row->right_key;
        $level         = $primary_row->level;
        $secondary_row = $this->clear()->where(['id' => $pid])->getOne();
        $left_idp  = $secondary_row->left_key;
        $right_idp = $secondary_row->right_key;
        $levelp    = $secondary_row->level;
        if ($pk == $pid || $left_id == $left_idp || ($left_idp >= $left_id && $left_idp <= $right_id) || ($level == $levelp + 1 && $left_id > $left_idp && $right_id < $right_idp)) {
            return false;
        }
        $sql = 'UPDATE ' . $this->table . ' SET ';
        if ($left_idp < $left_id && $right_idp > $right_id && $levelp < $level - 1) {
            $sql .= 'level = CASE WHEN left_key BETWEEN ' . $left_id . ' AND ' . $right_id . ' THEN level' . sprintf('%+d', -($level - 1) + $levelp) . ' ELSE level END, ';
            $sql .= 'right_key = CASE WHEN right_key BETWEEN ' . ($right_id + 1) . ' AND ' . ($right_idp - 1) . ' THEN right_key-' . ($right_id - $left_id + 1) . ' ';
            $sql .= 'WHEN left_key BETWEEN ' . $left_id . ' AND ' . $right_id . ' THEN right_key+' . ((($right_idp - $right_id - $level + $levelp) / 2) * 2 + $level - $levelp - 1) . ' ELSE right_key END, ';
            $sql .= 'left_key = CASE WHEN left_key BETWEEN ' . ($right_id + 1) . ' AND ' . ($right_idp - 1) . ' THEN left_key-' . ($right_id - $left_id + 1) . ' ';
            $sql .= 'WHEN left_key BETWEEN ' . $left_id . ' AND ' . $right_id . ' THEN left_key+' . ((($right_idp - $right_id - $level + $levelp) / 2) * 2 + $level - $levelp - 1) . ' ELSE left_key END ';
            $sql .= 'WHERE left_key BETWEEN ' . ($left_idp + 1) . ' AND ' . ($right_idp - 1);
        } elseif ($left_idp < $left_id) {
            $sql .= 'level = CASE WHEN left_key BETWEEN ' . $left_id . ' AND ' . $right_id . ' THEN level' . sprintf('%+d', -($level - 1) + $levelp) . ' ELSE level END, ';
            $sql .= 'left_key = CASE WHEN left_key BETWEEN ' . $right_idp . ' AND ' . ($left_id - 1) . ' THEN left_key+' . ($right_id - $left_id + 1) . ' ';
            $sql .= 'WHEN left_key BETWEEN ' . $left_id . ' AND ' . $right_id . ' THEN left_key-' . ($left_id - $right_idp) . ' ELSE left_key END, ';
            $sql .= 'right_key = CASE WHEN right_key BETWEEN ' . $right_idp . ' AND ' . $left_id . ' THEN right_key+' . ($right_id - $left_id + 1) . ' ';
            $sql .= 'WHEN right_key BETWEEN ' . $left_id . ' AND ' . $right_id . ' THEN right_key-' . ($left_id - $right_idp) . ' ELSE right_key END ';
            $sql .= 'WHERE (left_key BETWEEN ' . $left_idp . ' AND ' . $right_id . ' ';
            $sql .= 'OR right_key BETWEEN ' . $left_idp . ' AND ' . $right_id . ')';
        } else {
            $sql .= 'level = CASE WHEN left_key BETWEEN ' . $left_id . ' AND ' . $right_id . ' THEN level' . sprintf('%+d', -($level - 1) + $levelp) . ' ELSE level END, ';
            $sql .= 'left_key = CASE WHEN left_key BETWEEN ' . $right_id . ' AND ' . $right_idp . ' THEN left_key-' . ($right_id - $left_id + 1) . ' ';
            $sql .= 'WHEN left_key BETWEEN ' . $left_id . ' AND ' . $right_id . ' THEN left_key+' . ($right_idp - 1 - $right_id) . ' ELSE left_key END, ';
            $sql .= 'right_key = CASE WHEN right_key BETWEEN ' . ($right_id + 1) . ' AND ' . ($right_idp - 1) . ' THEN right_key-' . ($right_id - $left_id + 1) . ' ';
            $sql .= 'WHEN right_key BETWEEN ' . $left_id . ' AND ' . $right_id . ' THEN right_key+' . ($right_idp - 1 - $right_id) . ' ELSE right_key END ';
            $sql .= 'WHERE (left_key BETWEEN ' . $left_id . ' AND ' . $right_idp . ' ';
            $sql .= 'OR right_key BETWEEN ' . $left_id . ' AND ' . $right_idp . ')';
        }
        $this->query($sql);
        return true;
    }

    public function deleteNode($pk)
    {
        //получаем данные по первой строке..
        $primary_row = $this->clear()->where(['id' => $pk])->getOne();

        $left_id  = $primary_row->left_key;
        $right_id = $primary_row->right_key;

        $this->clear()->between('left_key', $left_id, $right_id)->delete();
        $delta_id = (($right_id - $left_id) + 1);
        $sql      = 'UPDATE ' . $this->table . ' SET ';
        $sql      .=  'left_key = CASE WHEN left_key > ' . $left_id . ' THEN left_key - ' . $delta_id . ' ELSE left_key END, ';
        $sql      .= 'right_key = CASE WHEN right_key > ' . $left_id . ' THEN right_key - ' . $delta_id . ' ELSE right_key END ';
        $sql      .= 'WHERE right_key > ' . $right_id;
        $this->query($sql);
        return true;
    }
}