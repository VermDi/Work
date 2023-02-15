<?php

namespace modules\lists\controllers;

use core\Controller;

class Index extends Controller
{

    public function actionIndex()
    {
        header('Location: /lists/lists/list');
    }

}