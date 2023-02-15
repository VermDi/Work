<?php

namespace modules\news\widgets;

use core\Html;
use modules\news\repositories\Articles;

/**
 * Created by PhpStorm.
 * User: Семья
 * Date: 08.03.2019
 * Time: 12:06
 */
class Show
{
    public static function LastNews($limit)
    {
        $ar = Articles::getActiveArticlesInCategory(0, $limit);
        return Html::instance()->render(__DIR__ . "/themes/apple/LastNews.php", $ar);
    }
}