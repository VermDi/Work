<?php
namespace core\helpers;
/**
 * Create by e-Mind Studio
 * User: E_dulentsov
 * Date: 23.10.2017
 * Time: 10:28
 */
class Nested
{

    public $pk_field = 'id';
    public $table = null;
    public $left = null;
    public $right = null;
    public $level = null;
    private $primary_row = null;
    private $secondary_row = null;
    /**
     * @var PDO
     */
    public $pdo = null;

    public function _check()
    {
        if ($this->table == null or empty($this->table)) {
            throw new Exception('Необходимо передать таблицу для работы');
        }
        if ($this->left == null or empty($this->left)) {
            throw new Exception('Необходимо левый ключ');
        }
        if ($this->right == null or empty($this->right)) {
            throw new Exception('Необходимо передать  правый ключ');
        }
        if ($this->level == null or empty($this->level)) {
            throw new Exception('Необходимо передать уровень');
        }
        if ($this->pdo == null or empty($this->pdo)) {
            throw new Exception('Необходим коннекор к PDO');
        }
    }

    /**
     * Перемещение ноды.
     *
     * @param $pk - чт перемещаем
     * @param $pid - куда перемещаем
     *
     * @throws Exception
     */
    public function moveNode($pk, $pid)
    {
        $this->_check();
        if (empty($pk)) {
            throw new Exception('НЕ передан индификатор первой строки (перемещаемого)');
        }
        if (empty($pid)) {
            throw new Exception('НЕ передан индификатор второй строки (получателя)');
        }
        //получаем данные по первой строке..
        $this->primary_row = $this->pdo->query("SELECT * FROM " . $this->table . " where " . $this->pk_field . " = '" . $pk . "'")
            ->fetch(PDO::FETCH_OBJ);
        //получаем данные по получателю
        $this->secondary_row = $this->pdo->query("SELECT * FROM " . $this->table . " where " . $this->pk_field . " = '" . $pid . "'")
            ->fetch(PDO::FETCH_OBJ);
        $left_id = $this->primary_row->{$this->left};
        $right_id = $this->primary_row->{$this->right};
        $level = $this->primary_row->{$this->level};
        $left_idp = $this->secondary_row->{$this->left};
        $right_idp = $this->secondary_row->{$this->right};
        $levelp = $this->secondary_row->{$this->level};
        if ($pk == $pid || $left_id == $left_idp || ($left_idp >= $left_id && $left_idp <= $right_id) || ($level == $levelp + 1 && $left_id > $left_idp && $right_id < $right_idp)) {
            return false;
        }
        $sql = 'UPDATE ' . $this->table . ' SET ';
        if ($left_idp < $left_id && $right_idp > $right_id && $levelp < $level - 1) {
            $sql .= $this->level . ' = CASE WHEN ' . $this->left . ' BETWEEN ' . $left_id . ' AND ' . $right_id . ' THEN ' . $this->level . sprintf('%+d', -($level - 1) + $levelp) . ' ELSE ' . $this->level . ' END, ';
            $sql .= $this->right . ' = CASE WHEN ' . $this->right . ' BETWEEN ' . ($right_id + 1) . ' AND ' . ($right_idp - 1) . ' THEN ' . $this->right . '-' . ($right_id - $left_id + 1) . ' ';
            $sql .= 'WHEN ' . $this->left . ' BETWEEN ' . $left_id . ' AND ' . $right_id . ' THEN ' . $this->right . '+' . ((($right_idp - $right_id - $level + $levelp) / 2) * 2 + $level - $levelp - 1) . ' ELSE ' . $this->right . ' END, ';
            $sql .= $this->left . ' = CASE WHEN ' . $this->left . ' BETWEEN ' . ($right_id + 1) . ' AND ' . ($right_idp - 1) . ' THEN ' . $this->left . '-' . ($right_id - $left_id + 1) . ' ';
            $sql .= 'WHEN ' . $this->left . ' BETWEEN ' . $left_id . ' AND ' . $right_id . ' THEN ' . $this->left . '+' . ((($right_idp - $right_id - $level + $levelp) / 2) * 2 + $level - $levelp - 1) . ' ELSE ' . $this->left . ' END ';
            $sql .= 'WHERE ' . $this->left . ' BETWEEN ' . ($left_idp + 1) . ' AND ' . ($right_idp - 1);
        } elseif ($left_idp < $left_id) {
            $sql .= $this->level . ' = CASE WHEN ' . $this->left . ' BETWEEN ' . $left_id . ' AND ' . $right_id . ' THEN ' . $this->level . sprintf('%+d', -($level - 1) + $levelp) . ' ELSE ' . $this->level . ' END, ';
            $sql .= $this->left . ' = CASE WHEN ' . $this->left . ' BETWEEN ' . $right_idp . ' AND ' . ($left_id - 1) . ' THEN ' . $this->left . '+' . ($right_id - $left_id + 1) . ' ';
            $sql .= 'WHEN ' . $this->left . ' BETWEEN ' . $left_id . ' AND ' . $right_id . ' THEN ' . $this->left . '-' . ($left_id - $right_idp) . ' ELSE ' . $this->left . ' END, ';
            $sql .= $this->right . ' = CASE WHEN ' . $this->right . ' BETWEEN ' . $right_idp . ' AND ' . $left_id . ' THEN ' . $this->right . '+' . ($right_id - $left_id + 1) . ' ';
            $sql .= 'WHEN ' . $this->right . ' BETWEEN ' . $left_id . ' AND ' . $right_id . ' THEN ' . $this->right . '-' . ($left_id - $right_idp) . ' ELSE ' . $this->right . ' END ';
            $sql .= 'WHERE (' . $this->left . ' BETWEEN ' . $left_idp . ' AND ' . $right_id . ' ';
            $sql .= 'OR ' . $this->right . ' BETWEEN ' . $left_idp . ' AND ' . $right_id . ')';
        } else {
            $sql .= $this->level . ' = CASE WHEN ' . $this->left . ' BETWEEN ' . $left_id . ' AND ' . $right_id . ' THEN ' . $this->level . sprintf('%+d', -($level - 1) + $levelp) . ' ELSE ' . $this->level . ' END, ';
            $sql .= $this->left . ' = CASE WHEN ' . $this->left . ' BETWEEN ' . $right_id . ' AND ' . $right_idp . ' THEN ' . $this->left . '-' . ($right_id - $left_id + 1) . ' ';
            $sql .= 'WHEN ' . $this->left . ' BETWEEN ' . $left_id . ' AND ' . $right_id . ' THEN ' . $this->left . '+' . ($right_idp - 1 - $right_id) . ' ELSE ' . $this->left . ' END, ';
            $sql .= $this->right . ' = CASE WHEN ' . $this->right . ' BETWEEN ' . ($right_id + 1) . ' AND ' . ($right_idp - 1) . ' THEN ' . $this->right . '-' . ($right_id - $left_id + 1) . ' ';
            $sql .= 'WHEN ' . $this->right . ' BETWEEN ' . $left_id . ' AND ' . $right_id . ' THEN ' . $this->right . '+' . ($right_idp - 1 - $right_id) . ' ELSE ' . $this->right . ' END ';
            $sql .= 'WHERE (' . $this->left . ' BETWEEN ' . $left_id . ' AND ' . $right_idp . ' ';
            $sql .= 'OR ' . $this->right . ' BETWEEN ' . $left_id . ' AND ' . $right_idp . ')';
        }
        $this->pdo->query($sql);
        return true;
    }

    /**
     * Удаляет элемент ветки
     * @param $pk
     * @param string $condition
     * @return bool
     */
    public function delete($pk, $condition = '')
    {
        //получаем данные по первой строке..
        $this->primary_row = $this->pdo->query("SELECT * FROM " . $this->table . " where " . $this->pk_field . " = '" . $pk . "'")
            ->fetch(PDO::FETCH_OBJ);
        $left_id = $this->primary_row->{$this->left};
        $right_id = $this->primary_row->{$this->right};
        $sql = 'DELETE FROM ' . $this->table . ' WHERE ' . $this->pk_field . ' = ' . $pk;
        $this->pdo->query($sql);
        $sql = 'UPDATE ' . $this->table . ' SET ';
        $sql .= $this->level . ' = CASE WHEN ' . $this->left . ' BETWEEN ' . $left_id . ' AND ' . $right_id . ' THEN ' . $this->level . ' - 1 ELSE ' . $this->level . ' END, ';
        $sql .= $this->right . ' = CASE WHEN ' . $this->right . ' BETWEEN ' . $left_id . ' AND ' . $right_id . ' THEN ' . $this->right . ' - 1 ';
        $sql .= 'WHEN ' . $this->right . ' > ' . $right_id . ' THEN ' . $this->right . ' - 2 ELSE ' . $this->right . ' END, ';
        $sql .= $this->left . ' = CASE WHEN ' . $this->left . ' BETWEEN ' . $left_id . ' AND ' . $right_id . ' THEN ' . $this->left . ' - 1 ';
        $sql .= 'WHEN ' . $this->left . ' > ' . $right_id . ' THEN ' . $this->left . ' - 2 ELSE ' . $this->left . ' END ';
        $sql .= 'WHERE ' . $this->right . ' > ' . $left_id;
        $sql .= $condition;
        $this->pdo->query($sql);
        return true;
    }

    /**
     * Удаляет всю ветку.
     * @param $pk
     * @param string $condition
     * @return bool
     */
    public function deleteNode($pk, $condition = '')
    {
        //получаем данные по первой строке..
        $this->primary_row = $this->pdo->query("SELECT * FROM " . $this->table . " where " . $this->pk_field . " = '" . $pk . "'")
            ->fetch(PDO::FETCH_OBJ);

        $left_id = $this->primary_row->{$this->left};
        $right_id = $this->primary_row->{$this->right};

        $sql = 'DELETE FROM ' . $this->table . ' WHERE ' . $this->left . ' BETWEEN ' . $left_id . ' AND ' . $right_id;
        $sql .= $condition;
        $this->pdo->query($sql);

        $delta_id = (($right_id - $left_id) + 1);
        $sql = 'UPDATE ' . $this->table . ' SET ';
        $sql .= $this->left . ' = CASE WHEN ' . $this->left . ' > ' . $left_id . ' THEN ' . $this->left . ' - ' . $delta_id . ' ELSE ' . $this->left . ' END, ';
        $sql .= $this->right . ' = CASE WHEN ' . $this->right . ' > ' . $left_id . ' THEN ' . $this->right . ' - ' . $delta_id . ' ELSE ' . $this->right . ' END ';
        $sql .= 'WHERE ' . $this->right . ' > ' . $right_id;
        $sql .= $condition;
        $this->pdo->query($sql);
        return true;
    }

    /**
     * Переносит ветку в рут корень
     * @param $pk
     * @return bool
     */
    public function makeNodeRoot($pk)
    {
        //получаем данные по строке
        $this->primary_row = $this->pdo->query("SELECT * FROM " . $this->table . " where " . $this->pk_field . " = '" . $pk . "'")
            ->fetch(PDO::FETCH_OBJ);
        //получаем максимальную правую границу
        $this->max_right = $this->pdo->query("SELECT max(" . $this->right . ") as max FROM " . $this->table)
            ->fetch(PDO::FETCH_OBJ);
        //вычисляем сдвиг.. куда перемещать
        $moveRange = $this->max_right->max - $this->primary_row->{$this->left} + 1;
        //вычисляем ворота
        $range = $this->primary_row->{$this->right} - $this->primary_row->{$this->left};
        //перемещаем
        $sql = 'UPDATE `' . $this->table . '`';
        $sql .= ' SET `' . $this->left . '` = `' . $this->left . '` + ' . $moveRange . ', ';
        $sql .= '`' . $this->right . '` = `' . $this->right . '` + ' . $moveRange . ', ';
        $sql .= '`' . $this->level . '` = `' . $this->level . '` - ' . $this->primary_row->level;
        $sql .= ' WHERE `' . $this->left . '` BETWEEN ' . $this->primary_row->{$this->left} . ' AND ' . $this->primary_row->{$this->right};
        $this->pdo->query($sql);
        // закрываем дыру
        $sql = 'UPDATE `' . $this->table . '`';
        $sql .= 'SET `' . $this->left . '` = IF (`' . $this->left . '` > ' . $this->primary_row->{$this->left} . ',`' . $this->left . '` - ' . ($range + 1) . ',' . $this->left . '),';
        $sql .= '`' . $this->right . '` = `' . $this->right . '` - ' . ($range + 1) . ' ';
        $sql .= 'WHERE `' . $this->right . '` > ' . $this->primary_row->{$this->left};
        echo $sql;
        $this->pdo->query($sql);
        return true;

    }
}