<?php
/**
 * Created by PhpStorm.
 * User: Евгения
 * Date: 21.09.2017
 * Time: 17:18
 */

namespace modules\user\widgets;


use modules\user\models\USER;

class ChildrenTree
{
    public static function printSimpleTree()
    {

        $user     = USER::current();
        $children = $user->getChildren();
        if (!empty($children)) {
            echo '<div class="children-tree">';
                self::printChildren($children);
            echo "</div>";
        }
    }

    private static function printChildren($array, $level = false, $parent = false, $i = 0)
    {
        $min_level = 1;
        if ($level == false) {
            $level = 999;
            foreach ($array as $k => $v) {
                if ($v->level < $level) {
                    $level     = $v->level; //получаем минимальный уровень
                    $min_level = $v->level;
                }
            }
        };

        foreach ($array as $v) {
            if ($v->level != $level) {
                continue;
            }
            if ($parent != false and ($v->left_key < $parent->left_key or $v->right_key > $parent->right_key)) {
                continue;
            }
            if ($min_level == $v->level) {
                $i = 0;
            }else{
                $i=$v->level-$min_level;
            }
            echo "<div data-id='" . $v->id . "' style='padding-left:" . (10 * $i) . "px;' class='user-profile' >" . $v->email . "</div>";

            self::printChildren($array, $level + 1, $v, $i);

        }
    }

}