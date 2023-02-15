<?php




namespace modules\migrations\controllers;

use core\Controller;
use core\Db;
use core\Html;
use core\Migration;
use core\User;

/**
 * @property-read  Migration $migration
 * Class Index
 * @package modules\migrations\controllers
 */
class Index extends Controller
{
    public $migration;

    function __construct()
    {
        parent::__construct();
        $this->migration = new Migration();
    }

    public function actionIndex($limit = false)
    {
        if (\modules\user\models\USER::isAuthorized()) {
            $this->migration->checkEnvironmentNoConsole();
            Html::instance()->title = 'Миграции';
            Html::instance()->setCss("/assets/vendors/jquery-ui-1.12.1.custom/jquery-ui.css");
            Html::instance()->setCss('/assets/vendors/datetimepicker-master/jquery.datetimepicker.css');
            Html::instance()->setJs('/assets/vendors/moment-js/moment.js');
            Html::instance()->setJs('/assets/vendors/moment-js/locale/ru.js');
            Html::instance()->setJs('/assets/vendors/datetimepicker-master/jquery.datetimepicker.js');
            Html::instance()->setCss('/assets/vendors/datatables/css/dataTables.bootstrap.min.css');
            Html::instance()->setJs("/assets/vendors/datatables/js/jquery.dataTables.min.js");
            Html::instance()->setJs("/assets/vendors/datatables/js/dataTables.bootstrap.js");
            Html::instance()->setJs("/assets/vendors/select2/js/select2.js");
            Html::instance()->setCss("/assets/modules/migrations/css/style.css");
            Html::instance()->setJs("/assets/modules/migrations/js/script.js");
            Html::instance()->setCss("/assets/vendors/select2/css/select2.min.css");
            Html::instance()->content = $this->render("List.php", $this->migration->getAll());
            Html::instance()->renderTemplate("@blank")->show();
        } elseif (!DB::instance()->query('Show tables')->fetchAll()) {
            header('Location: /migrations/up');
        } else {
            header('Location: /user/login');
        }
        exit;
    }

    public function actionScan()
    {
        sleep(1);
        $result = $this->migration->getNewMigrations();
        echo $this->render('ScanTable.php', $result);
        exit;
    }

    public function postScan()
    {

        sleep(1);
        $migrations = $this->migration->getNewMigrations();
        $result     = [];
        foreach ($migrations as $migration) {
            if (!$this->migration->migrateUpNoConsole($migration['class'], $migration['module'])) {
                $result[$migration['class']] = ['type' => 'danger', 'message' => $this->migration->errorInfo];
            } else {
                $result[$migration['class']] = ['type' => 'success', 'message' => 'OK'];
            }
        }
        echo json_encode($result);
        exit;
    }

    public function actionDown($limit = 1)
    {

        $result = [];
        if ($limit === 'all') {
            $limit = null;
        } else {
            $limit = (int)$limit;
            if ($limit < 1) {
                $result['error'][] = 'Кол-вот откатываемых миграций должно быть больше 0';
                echo json_encode($result);
                exit;
            }
        }

        $migrations = $this->migration->getMigrationHistory($limit);
        if (empty($migrations)) {
            $result['error'][] = 'Нет миграций для отката';
        }

        foreach ($migrations as $key => $migration) {
            if (!$this->migration->migrateDownNoConsole($key, $migration['module'])) {
                $result['error'][] = 'Не получилось откатить: ' . $key;
            } else {
                $result['success'] = 'ok';
            }
        }

        echo json_encode($result);
        exit;

    }

    public function actionCreateForm()
    {
        $modules = array();

        $handle = _MOD_PATH_;
        foreach (glob($handle . '*', GLOB_ONLYDIR | GLOB_MARK) as $dir) {
            $segments     = explode(DIRECTORY_SEPARATOR, $dir);
            $last_element = array_pop($segments);
            if (empty($last_element)) {
                $last_element = array_pop($segments);
            }
            $modules[] = $last_element;
        }
        echo $this->render('createForm.php', ['modules' => $modules]);
        exit;
    }

    public function actionCreate()
    {
        $name = '';
        if (!empty($_POST['module'])) {
            $name = $_POST['module'] . '/';
        };
        $name .= $_POST['name'];
        $this->migration->createNoConsole($name);
        echo "OK";
    }

    public function actionDownForm()
    {
        echo $this->render('downForm.php');
        exit;
    }

}