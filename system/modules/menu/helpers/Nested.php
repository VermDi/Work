<?php

namespace modules\menu\helpers;

use core\App;

/**
 * Created by PhpStorm.
 * User: E_dulentsov
 * Date: 13.06.2017
 * Time: 12:53
 */
class Nested
{
    /**
     * Работает :)
     * @param $array
     * @param $level
     * @param bool $parent
     */
    public static function printNestedTree($array, $level, $parent = false)
    {

        echo "<ul>";

        foreach ($array as $v) {
            if ($v->level != $level) {
                continue;
            }
            if ($parent != false and ($v->left_key < $parent->left_key or $v->right_key > $parent->right_key)) {
                continue;
            }
            echo "<li id='" . $v->id . "' data-position='" . ((isset($v->position)) ? $v->position : '0') . "'>";
            if (isset($v->visible) && $v->visible == 0) {
                echo " <span style='text-decoration:line-through;'>" . "[" . $v->id . "] " . $v->name . "</span>";
            } else {
                echo "[" . $v->id . "] " . $v->name;
            }


            self::printNestedTree($array, $level + 1, $v);
            echo "</li>";
        }
        echo "</ul>";

    }

    public static function printNestedTreeForMenu($array, $level = false, $parent = false, $ulClass = "sidebar-menu", $liClass = "", $activeLiClass = "")
    {

        if (is_array($array) and count($array) > 0) {
            if (!empty($ulClass)) {
                $ulClass = " class='" . $ulClass . "' ";
            }

            $ul = "<ul" . $ulClass . "id=\"nav-accordion\">";

            if ($level == false) {
                $level = 999;
                foreach ($array as $k => $v) {
                    if ($v->level < $level) {
                        $level = $v->level; //получаем минимальный уровень
                    }
                }
            }
            echo $ul;
            foreach ($array as $k => $v) {
                if ($v->level != $level) {
                    continue;
                }
                if ($parent != false and ($v->left_key < $parent->left_key or $v->right_key > $parent->right_key)) {
                    continue;
                }
                $active = "";
                if ($v->url == App::$url['path']) {
                    $active = $activeLiClass;
                }
                unset($array[$k]); //отработанные элемнты нам уже не нужны
                echo "<li class='" . $liClass . " " . $active . "' id='" . $v->id . "' data-position='" . $v->position . "'> <a href='" . $v->url . "'>" . $v->name . "</a>";
                self::printNestedTreeForMenu($array, $level + 1, $v, 'rd-navbar-dropdown');
                echo "</li>";
            }
            echo "</ul>";
        }

    }
}