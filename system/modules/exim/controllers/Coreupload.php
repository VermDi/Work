<?php

namespace modules\exim\controllers;

use core\Controller;
use core\FS;
use core\Html;
use core\Parameters;
use modules\exim\helpers\EximHelper;
use modules\exim\models\mModules;

class Coreupload extends Controller
{

    public function actionIndex()
    {
        header('Location: /exim/coreupload/list');
    }

    /**
     * Форма загрузки ядра на сервер
     */
    public function actionForm()
    {
        $data = [];
        $data['supportSettings'] = \core\Parameters::get('supportModules');
        /*
         * Получить текущий файл конфигурации version.php
         * Если есть валидный version.php то возьмем его
         */
        if ($EximCustom = EximHelper::instance()->getEximCustom('core1')) {
            if (EximHelper::instance()->checkEximValidate($EximCustom, true)) {
                $data['EximConfig'] = $EximCustom;
            }
        }


        echo $this->render("/exim/FormCoreUpload.php", $data);
        die();
    }

    /**
     * Сохранить файл версии /core/version.php для ядра и отправить на сервер
     */
    public function actionSave()
    {
        $data = $_POST;

        if (empty($data['version'])) {
            echo json_encode(['error' => 1, 'data' => 'Введите версию']);
            exit();
        }
        if (empty($data['version_description'])) {
            echo json_encode(['error' => 1, 'data' => 'Введите Описание версии']);
            exit();
        }
        if (empty($data['Folders'])) {
            echo json_encode(['error' => 1, 'data' => 'Введите список папок']);
            exit();
        }
        $FoldersArr = explode("\n", $data['Folders']);
        $Folders = [];
        foreach ($FoldersArr as $Folder){
            $Folder = str_replace("\n", "", $Folder);
            $Folder = str_replace("\r", "", $Folder);

            if (!file_exists(__DIR__ . '/../../../../'.$Folder)) {
                echo json_encode(['error' => 1, 'data' => 'Не найден '.$Folder]);
                exit();
            }
            $Folders[]=$Folder;
        }

        /*
         * Создадим файл версии ядра
         */
        $exim_module = __DIR__ . '/../../../core/version.php';
        $dataVersion=[];
        $dataVersion['nameModule'] = 'core';
        $dataVersion['version']=$data['version'];
        $dataVersion['version_description']=$data['version_description'];
        $dataVersion['Folders']=$Folders;
        $dataVersion['getExample'] = 1;
        file_put_contents($exim_module,  $this->render("/exim/DefaultVersion.php", $dataVersion));

        echo json_encode(['error' => 0, 'data' => 'Успешно']);
        exit();
    }

}