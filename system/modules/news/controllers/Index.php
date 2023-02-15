<?php
/**
 * Created by PhpStorm.
 * User: Семья
 * Date: 08.03.2019
 * Time: 11:16
 */

namespace modules\news\controllers;


use core\App;
use core\Controller;
use core\Errors;
use core\Html;
use modules\news\repositories\Articles;

class Index extends Controller
{
    public function actionIndex($alias = false)
    {
        /*
         * Список
         */
        Html::instance()->setNoWidgets();
        if ($alias == false) {
            Html::instance()->title = "Самые свежие новости";
            Html::instance()->content = $this->render("/themes/" . $this->config['theme'] . "/List.php", Articles::getPreviewActiveArticlesInCategory(0));
            Html::instance()->renderTemplate($this->config['default_template'])->show();
        }
        /*
         * Лишние параметры, ошибка
         */
        if ($alias != false and count(App::$url['way']) > 2) {
            Errors::e404();
        }
        /*
         * Новость
         */
        if ($alias != false and count(App::$url['way']) < 3) {
            /**
             * Смотрим статью
             */
            $article = Articles::getActiveArticle(App::$url['way'][1]);
            /*
             * Нет статьи, знать 404
             */
            if (!$article) {
                Errors::e404();
            }
            /*
             * Статья есть показываем
             */
            html()->title = $article->title;
            $meta = "";
            if (!empty($article->meta_keywords)) {
                $meta .= "<meta name=\"keywords\" content=\"" . $article->meta_keywords . "\" />";
            }
            if (!empty($article->meta_desc)) {
                $meta .= "<meta name=\"description\" content=\"" . $article->meta_desc . "\" />";
            }
            Html::instance()->meta = $meta;

            Html::instance()->content = $this->render("/themes/" . $this->config['theme'] . "/Article.php", $article);
            Html::instance()->renderTemplate($this->config['default_template'])->show();

        }
    }
}