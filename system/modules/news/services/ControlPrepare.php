<?php
/**
 * Created by PhpStorm.
 * User: dulentcov-smishko
 * Date: 06.03.2019
 * Time: 10:48
 */

namespace modules\news\services;


use modules\news\models\Article;

/**
 * Это слой для подготовки и операций не связаннных с получением или сохранением данных
 * но он готовит данные для тех или иных действий
 *
 * Class ControlPrepare
 * @package modules\news\services
 */
class ControlPrepare
{
    public static function setControl($arr)
    {
        $new_arr = [];
        if (is_array($arr) and count($arr) > 0) {
            foreach ($arr as $k => $v) {
                $v->trash = "<span class='btn btn-danger btn-xs' onclick='delArticle(\"" . $v->id . "\");' ><i class=\"fa fa-trash\"></i></span>";
                $v->edit = "<span class='btn btn-warning btn-xs' onclick='showArticleForm(\"" . $v->id . "\");'><i class=\"fa fa-edit\"></i></span>";
                if (file_exists(Article::instance()->path_to_image . $v->id . "/" . $v->id . ".jpg")) {
                    $v->image = "<i class=\"fa fa-image\"></i>";
                } else {
                    $v->image = "";
                }
                if ($v->visible == 1) {
                    $v->visible = "<i class=\"fa fa-eye\"></i>";
                } else {
                    $v->visible = "<i class=\"fa fa-eye-slash\"></i>";
                }
                $new_arr[$k] = $v;
            }
        }
        return $new_arr;
    }

    /**
     * Данный метод чинит новости если их просто залили в базу напрямую
     */
    public static function getRepairAfterTransfer()
    {
        $art = Articles::getAllArticlesInCategory();
        foreach ($art as $article) {
            $a = new Article();
            $a->factory()->fill($article);
            $a->short_article = htmlspecialchars_decode($a->short_article);
            $a->full_article = htmlspecialchars_decode($a->full_article);
            $a->save();
            echo "Закончил " . $a->id . "\n\r<br>";
        }
    }

}