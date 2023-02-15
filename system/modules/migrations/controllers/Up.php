<?php
/**
 * Created by PhpStorm.
 * User: Евгения
 * Date: 10.10.2017
 * Time: 13:50
 */

namespace modules\migrations\controllers;


use core\Controller;
use core\Html;
use core\Migration;

/**
 * @property-read Migration $migration
 * Class Up
 * @package modules\migrations\controllers
 */
class Up extends Controller
{
    public $migration;

    function __construct()
    {
        parent::__construct();
        $this->migration = new Migration();
    }

    public function actionIndex()
    {

        Html::instance()->title = "Установка миграций";
        Html::instance()->setJs('/assets/vendors/Jquery/jquery.progresstimer.min.js');
        Html::instance()->setJs('/assets/modules/migrations/js/progress.js');
        Html::instance()->content = $this->render('default.php');
        Html::instance()->renderTemplate('@blank')->show();

    }

    public function actionProgress()
    {
        $this->migration->checkEnvironmentNoConsole();
        sleep(1);
        $migrations = $this->migration->getNewMigrations();
        foreach ($migrations as $migration) {
            if (!$this->migration->migrateUpNoConsole($migration['class'], $migration['module'])) {
                echo $this->migration->errorInfo;
            } else {
                echo "OK";
            }
        }
        exit;
    }
}