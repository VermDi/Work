<?php
/**
 * Created by PhpStorm.
 * User: dulentcov-smishko
 * Date: 14.09.2018
 * Time: 18:03
 */

namespace modules\systemtests\tests;


use core\Tests;
use modules\user\models\USER;
use modules\user\models\UserPermission;

class userTests extends Tests
{
    public function run()
    {
        if (empty($_SESSION['user'])) {
            throw new \Exception('Ошибка! У пользователя нет сессии с ним! ');
        }
        return $this->result;
    }
}