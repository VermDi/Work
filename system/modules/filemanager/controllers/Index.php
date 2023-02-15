<?php

namespace modules\filemanager\controllers;

use core\Controller;

class Index extends Controller
{
    public function actionIndex()
    {
        $start_path = substr($_SERVER["DOCUMENT_ROOT"], 0, strrpos($_SERVER["DOCUMENT_ROOT"], DIRECTORY_SEPARATOR));
        /*
        * Устанавливаем дизайн
        */
        \core\Html::instance()->title = "Mind CMS - Файловый менеджер";

        /*
         * Проверяем права
         */
        if (!\modules\user\models\USER::current()) {
            die();
        }


        \core\Html::instance()->setJs("/assets/modules/filemanager/filemanager.js");
        \core\Html::instance()->content = \core\Html::instance()->render(__DIR__ . "/../templates/index.php");
        \core\Html::instance()->renderTemplate("@admin")->show();
    }
}
