<?php

namespace modules\exim\controllers;

use core\Controller;
use core\FS;
use core\Html;
use core\Parameters;
use modules\exim\helpers\EximHelper;
use modules\exim\models\mModules;

class Manifest extends Controller
{
    public $rootDir = __DIR__ . '/../../../../';

    public function actionIndex()
    {
        header('Location: /exim');
    }

    /**
     * Форма файла манифеста который содержит важную информацию о модуле
     */
    public function actionForm($nameModule)
    {
        $data = [];
        $data['EximConfig'] = EximHelper::instance()->getEximConfig($nameModule);
        $data['modules'] = mModules::instance()->getModules();
        echo $this->render("/manifest/Form.php", $data);
        die();
    }

    /**
     * Создание файла version.php в модуле
     */
    public function actionSave($nameModule)
    {
        $EximConfig = EximHelper::instance()->getEximDefault($nameModule);
        $data = $_POST;

        $EximConfig['EximConfig']['version'] = $data['version'];
        $EximConfig['EximConfig']['ModuleInfo']['version_description'] = $data['version_description'];
        $EximConfig['EximConfig']['Folders'] = $data['Folders'];
        $EximConfig['EximConfig']['requireModules']=[];
        if(!empty($data['modules'])){
            foreach ($data['modules'] as $module){
                $moduleArr = explode("_v_", $module);
                if(!empty($moduleArr[0]) && !empty($moduleArr[1])){
                    $EximConfig['EximConfig']['requireModules'][]=[
                        'name_module' => $moduleArr[0],
                        'version' => $moduleArr[1],
                    ];
                }
            }
        }

        $exim_module = __DIR__ . '/../../../modules/' . $nameModule . '/version.php';

        $dataConfig=$EximConfig['EximConfig'];
        $dataConfig['nameModule'] = $nameModule;
        $dataConfig['getExample'] = 1;

        /*echo '<pre>';
        print_r($EximConfig);
        echo '</pre>';*/
        file_put_contents($exim_module, $this->render("/exim/DefaultVersion.php", $dataConfig));

        /*$fp = fopen($exim_module, "w");
        fwrite($fp, EximHelper::instance()->ArrayExport($EximConfig));
        fclose($fp);*/

        echo json_encode(['error' => 0, 'data' => 'Успешно']);
        exit();
    }

    public function actionGetfolders()
    {
        if (empty($_POST['path'])) {
            $path = $this->rootDir;
            $pathPost = '';
        } else {
            $path = $this->rootDir . $_POST['path'];
            $pathPost = $_POST['path'];
        }

        if (file_exists($path) && is_file($path)) {
            $files = [];
        } else {
            $files = scandir($path);
            if (isset($files[0])) {
                unset($files[0]);
            }
            if (isset($files[1])) {
                unset($files[1]);
            }
        }

        echo json_encode(['error' => 0, 'data' => 'Успешно', 'path' => $pathPost, 'files' => $files]);
        exit();

        echo '<pre>';
        print_r($files);
        echo '</pre>';
    }

}