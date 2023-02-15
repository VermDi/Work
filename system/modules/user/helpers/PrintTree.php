<?php

namespace modules\user\helpers;
/**
 * Create by e-Mind Studio
 * User: Евгения
 * Date: 16.06.2017
 * Time: 12:59
 */
class PrintTree
{

    public $classUl = "";

    public function printTree($array, $level, $parent = false)
    {
        echo "<ul " . (($this->classUl) ? "class='" . $this->classUl . "'" : "") . ">";
        foreach ($array as $v) {

            if ($v->level != $level) {
                continue;
            }
            if ($parent != false and ($v->left_key < $parent->left_key or $v->right_key > $parent->right_key)) {
                continue;
            }

            echo "<li id='" . $v->id . "' data-id='" . $v->id . "' data-title='Добавить пользователя'>".$v->email;
            $this->printTree($array, $level + 1,$v);
            echo "</li>";


        }
        echo "</ul>";
    }
}
