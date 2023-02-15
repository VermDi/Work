<?php namespace modules\gkg\controllers;

use core\Controller;
use core\Db;
use core\Html;
use core\Tools;
use forms\Forms;
use modules\gkg\models\Generator;

class Admin extends Controller
{

    function actionIndex()
    {
        Html::instance()->content = $this->render("/admin/Adminindex.php");
        Html::instance()->renderTemplate("@admin")
            ->show();
    }

    function actionGenerate()
    {
        $act = $this->app->request->GKGact;
        $name = $this->app->request->GKGName;
        $moduleName = $name;
        $table = $this->app->request->GKGtable;
        $folder = $this->app->request->GKGfolder;
        $modName = ucfirst(strtolower($this->app->request->GKGmodel_name));
        $conName = ucfirst(strtolower($this->app->request->GKGcontroller_name));
        $temName = ucfirst(strtolower($this->app->request->GKGtemplate_name));
        $temlpateListName = ucfirst(strtolower($this->app->request->GKGtemplateList_name));

        switch ($act) {
            case 'module':

                if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/../system/modules/" . $name)) {
                    mkdir($_SERVER['DOCUMENT_ROOT'] . "/../system/modules/" . $name, '0777');
                    file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/../system/modules/" . $name . "/boot.php", "<?php " . $this->render('/admin/Boot.php') . "");
                    //file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/../system/modules/" . $name . "/info.php", "<?" . $this->render('/admin/Info.php'));
                    file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/../system/modules/" . $name . "/permissions.php", "<?" . $this->render('/admin/Permissions.php'));
                    file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/../system/modules/" . $name . "/set.php", "<?" . $this->render('/admin/Set.php'));
                    //file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/../system/modules/" . $name . "/version.php", "<?" . $this->render('/admin/Version.php', ['name' => $name]));
                }
                if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/../system/modules/" . $name . "/controllers/")) {
                    mkdir($_SERVER['DOCUMENT_ROOT'] . "/../system/modules/" . $name . "/controllers/", '0777');
                }
                if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/../system/modules/" . $name . "/models/")) {
                    mkdir($_SERVER['DOCUMENT_ROOT'] . "/../system/modules/" . $name . "/models/", '0777');
                }
                if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/../system/modules/" . $name . "/templates/")) {
                    mkdir($_SERVER['DOCUMENT_ROOT'] . "/../system/modules/" . $name . "/templates/", '0777');
                }
                $data = $_POST;
                $data['moduleName'] = $moduleName;
                $data['modName'] = $modName;
                $data['conName'] = $conName;
                $data['temName'] = $temName;
                $data['temListName'] = $temlpateListName;
                if (!empty($conName)) {
                    $file_controller = $_SERVER['DOCUMENT_ROOT'] . "/../system/modules/" . $name . "/controllers/" . $conName . ".php";
                    if (!file_exists($file_controller)) {
                        file_put_contents($file_controller, $this->render("/admin/ControllerGenerate.php", $data));
                        echo "Контроллер сделал <br>" . PHP_EOL;
                    } else {
                        echo " ФАйл есть, контроллер не делаю <br>" . $file_controller;
                    }
                }
                if (!empty($modName)) {
                    $file_model = $_SERVER['DOCUMENT_ROOT'] . "/../system/modules/" . $name . "/models/" . $modName . ".php";
                    if (!file_exists($file_model)) {
                        file_put_contents($file_model, $this->render("/admin/ModelGenerate.php", $data));
                        echo "Модель сделал <br>" . PHP_EOL;
                    } else {
                        echo " ФАйл есть, модель не делаю <br>" . $file_model;
                    }
                }
                if (!empty($temName)) {
                    $file_view = $_SERVER['DOCUMENT_ROOT'] . "/../system/modules/" . $name . "/templates/" . $temName . ".php";
                    if (!file_exists($file_view)) {
                        file_put_contents($file_view, $this->render("/admin/FormGenerate.php", $data));
                        echo "Темплейт сделал Формы <br>" . PHP_EOL;
                    } else {
                        echo " ФАЙЛ ЕСТЬ, темплейт формы не делаю <br>" . $file_view;
                    }
                }
                if (!empty($temlpateListName)) {
                    $file_view = $_SERVER['DOCUMENT_ROOT'] . "/../system/modules/" . $name . "/templates/" . $temlpateListName . ".php";
                    $loadFileForm = $_SERVER['DOCUMENT_ROOT'] . "/../system/modules/" . $name . "/templates/LoadFileForm.php";
                    if (!file_exists($loadFileForm)) {
                        file_put_contents($loadFileForm, $this->render("/admin/LoadFileForm.php", $data));
                    }
                    if (!file_exists($file_view)) {
                        file_put_contents($file_view, $this->render("/admin/ListTemplateDataTables.php", $data));
                        echo "Темплейт списка сделал<br>" . PHP_EOL;
                    } else {
                        echo " Файл  уже есть, темплейт списка не создаю <br>" . $file_view;
                    }
                }
                file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/../system/modules/" . $name . "/rights.php", "<?php" . $this->render('/admin/Rights.php', ['c' => $conName, 'm' => $moduleName]));
                $timeMigration = gmdate('ymd_His');


                if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/../system/modules/" . $name . "/migrations/")) {

                    mkdir($_SERVER['DOCUMENT_ROOT'] . "/../system/modules/" . $name . "/migrations/", '0777');

                    $timeMigration = gmdate('ymd_His', time() + 100);
                    file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/../system/modules/" . $name . "/migrations/m" . $timeMigration . "_Install.php", "<?php" . $this->render('/admin/Migration.php', ['c' => $conName, 'm' => $moduleName, 't' => $timeMigration]));
                }


                echo "<a href='/gkg/admin/'>Вернуться </a>";
                break;
            case 'form':
                if (empty($act) or empty($table) or empty($folder)) {
                    echo "НЕ передан один из параметров";
                } else {
                    file_put_contents($_SERVER['DOCUMENT_ROOT'] . "/../system/modules/" . $folder . "/templates/" . $name, $this->render("/admin/FormGenerate.php", $_POST));
                }
                break;
            default:
                die("PRIVET! something bad");
                break;
        }


    }

    public function actionLoadTableStructer($table = false)
    {
        if ($table == false) {
            echo "Укажите таблицу";
            die();
        }
        try {
            $rezult = DB::instance()
                ->query("SHOW FULL COLUMNS from " . $table)
                ->fetchAll();
        } catch (Exception $e) {
            echo 'Выброшено исключение: ', $e->getMessage(), "\n";
        }
        if ($rezult) {
//            echo "<pre>";
//            print_r($rezult);
            ?>
            <div class="clearfix">
            <legend>Если нужен шаблон, укажите поля.</legend><?
            foreach ($rezult as $k => $v) {
                ?>
                <div class="col-sm-4"> Для поля <?= $v[0]; ?>
                    <input type="hidden" name="<?= $v[0] ?>-comment" value="<?= htmlspecialchars($v['8']); ?>">
                    <select name="<?= $v[0] ?>" class="form-control">
                        <option value="-"> -</option>
                        <option value="Input">Input</option>
                        <option value="Textarea">Textarea</option>
                        <option value="Img">Img</option>
                        <option value="Radio">Radio</option>
                        <option value="Select">Select</option>
                        <option value="Checkbox">Checkbox</option>
                    </select>
                </div>
                <?
            }
            ?></div><?
            //echo $this->render("/admin/Choosetype.php", $rezult);
        } else {
            echo "НЕТ таблицы";
        }
        //die();
    }
}
