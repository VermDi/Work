<?php

namespace modules\backups\controllers;

use core\Controller;

class Index extends Controller
{

    public function actionIndex()
    {
        header('Location: /backups/backup/list');
    }

}