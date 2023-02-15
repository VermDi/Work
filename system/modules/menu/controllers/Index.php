<?php
/**
 * Created by PhpStorm.
 * User: E_dulentsov
 * Date: 09.06.2017
 * Time: 17:55
 */

namespace modules\menu\controllers;


class Index
{
    public function actionGetone()
    {

    }

    public function actionAll()
    {
        $res = [
            `id` => '1', 'text' => 'first', 'state' => [
                'opened' => true, 'disabled' => false, 'selected' => true],

        ];


        echo json_encode($res);
        die();

    }

}