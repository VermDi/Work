<?php

namespace modules\exim\controllers;

use core\Controller;
use core\FS;
use core\Html;
use core\Parameters;
use Curl\Curl;
use modules\exim\helpers\EximHelper;
use modules\exim\models\mModules;

class Index extends Controller
{

    public function actionIndex()
    {
        header('Location: /exim/list');
    }

    /**
     * @param int $status
     */
    public function actionListserver($status = 0)
    {
        $data = [];
        $data['status'] = $status;
        $data['supportSettings'] = Parameters::get('supportModules');

        Html::instance()->setJs("/assets/vendors/datatables/js/jquery.dataTables.min.js");
        Html::instance()->setCss("/assets/vendors/datatables/css/jquery.dataTables.min.css");

        //Html::instance()->setCss('/assets/vendors/jquery.datetimepicker/jquery.datetimepicker.css');
        //Html::instance()->setJs('/assets/vendors/jquery.datetimepicker/jquery.datetimepicker.js');

        Html::instance()->setCss("/assets/vendors/toastr/dist/jquery.toast.min.css");
        Html::instance()->setJs("/assets/vendors/toastr/dist/jquery.toast.min.js");

        Html::instance()->setJs("/assets/modules/exim/js/eximserver.js");
        Html::instance()->setJs("/assets/modules/exim/js/coreupload.js");
        Html::instance()->setJs("/assets/modules/exim/js/eximtoken.js");
        Html::instance()->setJs("/assets/modules/exim/js/install.js");
        Html::instance()->content = $this->render("/exim/Listserver.php", $data);
        Html::instance()->renderTemplate('@admin')->show();
    }

    /**
     * Создать ссылку www
     */
    public function actionSavewww()
    {
        $PathRootFolder = EximHelper::instance()->getPathRootFolder();
        $getNameRootFolder = EximHelper::instance()->getRootFolder();
        $folder1 = $PathRootFolder . 'www';
        $folder2 = $PathRootFolder . $getNameRootFolder;
        EximHelper::instance()->CreateLink($folder1, $folder2);
        echo json_encode(['error' => 0, 'data' => 'Успешно']);
        exit();
    }

    /**
     * Сохранить токен поддержки
     */
    public function actionSavetoken()
    {
        $data = $_POST;
        if (empty($data['token'])) {
            echo json_encode(['error' => 1, 'data' => 'Токен пустой']);
            exit();
        }
        /*
         * Проверим токен
         */
        $checkToken = EximHelper::instance()->checkToken(['token' => $data['token']]);
        if (!$checkToken) {
            echo json_encode(['error' => 1, 'data' => 'Токен не работает']);
            exit();
        }

        Parameters::set($data, 'supportModules');
        echo json_encode(['error' => 0, 'data' => 'Успешно']);
        exit;
    }

    /**
     * Получить удаленные модули с сервера
     */
    public function actionGetlistserver($getCore = false)
    {
        /*
         * Получаем список модулей
         */
        $Getlistserver = EximHelper::instance()->Getlistserver($getCore);
        if (!$Getlistserver) {
            echo json_encode(['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0]);
            exit;
        }
        echo $Getlistserver;
        exit;
    }

    /**
     * Страница локальных модулей
     * @param int $status
     */
    public function actionList($status = 0)
    {

        $data = [];
        $data['status'] = $status;
        $data['supportSettings'] = \core\Parameters::get('supportModules');

        Html::instance()->setJs("/assets/vendors/datatables/js/jquery.dataTables.min.js");
        Html::instance()->setCss("/assets/vendors/datatables/css/jquery.dataTables.min.css");

        //Html::instance()->setCss('/assets/vendors/jquery.datetimepicker/jquery.datetimepicker.css');
        //Html::instance()->setJs('/assets/vendors/jquery.datetimepicker/jquery.datetimepicker.js');

        Html::instance()->setCss("/assets/vendors/toastr/dist/jquery.toast.min.css");
        Html::instance()->setJs("/assets/vendors/toastr/dist/jquery.toast.min.js");

        Html::instance()->setJs("/assets/modules/exim/js/exim.js");
        Html::instance()->setJs("/assets/modules/exim/js/manifest.js");
        Html::instance()->setJs("/assets/modules/exim/js/coreupload.js");
        Html::instance()->setJs("/assets/modules/exim/js/eximtoken.js");
        Html::instance()->setJs("/assets/modules/exim/js/install.js");
        Html::instance()->content = $this->render("/exim/List.php", $data);
        Html::instance()->renderTemplate('@admin')->show();
    }

    /**
     * Получить список локальных модулей
     * @param bool $id
     * @param bool $getData
     * @return array
     */
    public function actionGetlist($id = false, $getData = false)
    {
        /*
        * для передачи по ajax в формет json
        */
        $options = [];
        if (!empty($id)) {
            $options['id'] = $id;
        }
        //$options['offset'] = 0;
        //$options['limit'] = 5;
        $res = mModules::instance()->getModules($options);
        $countAll = count($res);

        $checkCurrentToken = EximHelper::instance()->checkCurrentToken(['upload' => 1]);

        if (!empty($res)) {
            foreach ($res as $k => $v) {
                $file_exim = $v['file_exim'];
                $res[$k]['file_exim'] = ($file_exim == 'y') ? 'Файл найден' : '<a class="btn btn-info btn-xs CreateExim" data-id="' . $v['id'] . '" href="#">Создать файл</a>';
                $res[$k]['options'] = '<a data-id="' . $v['id'] . '" data-title="Инфо" class="btn btn-info btn-xs ajax" href="/exim/options/' . $v['id'] . '">Инфо</a>';
                $res[$k]['manifest'] = '<a data-id="' . $v['id'] . '" data-title="Инфо" class="btn btn-info btn-xs ajax" href="/exim/manifest/form/' . $v['id'] . '">Манифест</a>';

                $res[$k]['info'] = '<span class="infoServer"></span>';

                $res[$k]['version'] = '';
                if (!empty($v['exim_info']['version'])) {
                    $res[$k]['version'] = $v['exim_info']['version'];
                }

                $res[$k]['version_description'] = '';
                if (!empty($v['exim_info']['ModuleInfo']['version_description'])) {
                    $res[$k]['version_description'] = $v['exim_info']['ModuleInfo']['version_description'];
                }

                $res[$k]['control'] = '<a data-id="' . $v['id'] . '" class="btn btn-info btn-xs" href="/exim/download/' . $v['id'] . '">Скачать архив</a>';
                /*
                * Если есть рабочий токен то дадим возможность отправлять на сервер download
                */
                if ($checkCurrentToken) {
                    $res[$k]['control'] .= '<a data-id="' . $v['id'] . '" class="btn btn-info btn-xs uploadServer" href="/exim/upload/' . $v['id'] . '">Загрузить в облако</a>';
                }
                $res[$k]['control'] .= '<a data-id="' . $v['id'] . '" data-title="Удаление модуля" class="btn btn-danger btn-xs ajax" href="/exim/delmoduleform/' . $v['id'] . '"><i class="fa fa-trash-o"></i></a>';
            }
        } else {
            $res = [];
        }

        if (!empty($getData)) {
            return $res;
        } else {
            echo json_encode(['data' => $res, 'recordsTotal' => $countAll, 'recordsFiltered' => $countAll]);
        }
        die();
    }

    /**
     * Форма установки модулей
     */
    public function actionForm()
    {
        $data = [];
        $data['supportSettings'] = \core\Parameters::get('supportModules');
        echo $this->render("/exim/Form.php", $data);
        die();
    }

    /**
     * Форма удаления локального модуля
     * @param $nameModule
     */
    public function actionDelmoduleform($nameModule)
    {
        $DOCUMENT_ROOT_DIR = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
        $Destination = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'ziptemp' . DIRECTORY_SEPARATOR . $nameModule . '.zip';
        /*
         * $EximConfig
         */
        $EximConfig = EximHelper::instance()->getEximConfig($nameModule);

        $FoldersDefault = $EximConfig['Folders'];

        /*
         * Проверим папки на существование
         */
        $Folders = [];
        foreach ($FoldersDefault as $Folder) {
            if (file_exists($DOCUMENT_ROOT_DIR . $Folder)) {
                $Folders[] = $Folder;
            }
        }

        if (empty($Folders)) {
            echo 'Папки не найдены';
            exit;
        }

        $message = '<h2>Папки и файлы в них будут удалены:</h2>';
        foreach ($Folders as $Folder) {
            $message .= '<br><span style="color: red;">' . $Folder . '</span>';
        }
        $message .= '<p><a data-id="' . $nameModule . '" class="btn btn-success delModule" href="#">Удалить папки и файлы</a></p>';

        echo $message;
    }

    /**
     * Удалить модуль
     * @param $nameModule
     */
    public function actionDelmodule($nameModule)
    {
        $DOCUMENT_ROOT_DIR = realpath(__DIR__ . '/../../../../') . DIRECTORY_SEPARATOR;
        /*
         * $EximConfig
         */
        $EximConfig = EximHelper::instance()->getEximConfig($nameModule);

        $FoldersDefault = $EximConfig['Folders'];

        /*
         * Проверим папки на существование
         */
        $Folders = [];
        foreach ($FoldersDefault as $Folder) {
            if (file_exists($DOCUMENT_ROOT_DIR . $Folder)) {
                $Folders[] = $Folder;
            }
        }
        /*
         * Удалим папки
         */
        foreach ($Folders as $Folder) {
            FS::instance()->removeFolder($DOCUMENT_ROOT_DIR . $Folder, 1);
        }

        echo json_encode(['error' => 0, 'data' => 'Успешно']);
        exit();
    }

    public function actionFormtoken()
    {
        $data = [];
        $data['supportSettings'] = Parameters::get('supportModules');
        echo $this->render("/exim/Formtoken.php", $data);
        die();
    }

    /**
     * Форма создания модуля
     */
    public function actionFormmodule()
    {
        $data = [];
        echo $this->render("/exim/Formmodule.php", $data);
        die();
    }

    /**
     * Вывести текущую конфигурацию модуля
     * @param $nameModule
     */
    public function actionOptions($nameModule)
    {
        $EximConfig = EximHelper::instance()->getEximConfig($nameModule);
        echo '<pre>';
        print_r($EximConfig);
        echo '</pre>';
    }

    public function actionRemovetoken()
    {
        Parameters::set([1], 'supportModules');
        echo json_encode(['error' => 0, 'data' => 'Успешно']);
        exit;
    }

    /**
     * Создание файла version.php в модуле
     * @param $nameModule
     */
    public function actionCreateexim($nameModule)
    {
        $EximConfig = EximHelper::instance()->getEximDefault($nameModule);

        $exim_module = __DIR__ . '/../../../modules/' . $nameModule . '/version.php';
        if (file_exists($exim_module)) {
            echo json_encode(['error' => 1, 'data' => 'Уже есть файл']);
            exit();
        }

        $data['nameModule'] = $nameModule;
        $data['getExample'] = 1;
        file_put_contents($exim_module, $this->render("/exim/DefaultVersion.php", $data));

        /*$fp = fopen($exim_module, "w");
        fwrite($fp, EximHelper::instance()->ArrayExport($EximConfig));
        fclose($fp);*/

        echo json_encode(['error' => 0, 'data' => 'Успешно']);
        exit();
    }

    /**
     * Установление модуля из архива и с сервера поддержки
     * @param int $action
     */
    public function actionSave($action = 1)
    {
        $data = $_FILES;

        /*
         * Создадим папку если нет
         */
        $dir = __DIR__ . '/../ziptemp/';
        if (!file_exists($dir)) {
            if (!mkdir($dir, 0755, true)) {
                die('ERROR WITH RIGHTS');
            }
        }

        $uploaddir = __DIR__ . '/../ziptemp/';
        $DOCUMENT_ROOT_DIR = __DIR__ . '/../../../../';
        $versionInfoArr = [];
        /*
         * Если удаленный модуль
         */
        $fileName = '';
        $data['supportSettings'] = Parameters::get('supportModules');
        if (!empty($_POST['url'])) {
            if (!isset($data['supportSettings']->token)) {
                echo json_encode(['error' => 1, 'data' => 'Токен не найден']);
                exit();
            }

            $url = $_POST['url'] . '?token=' . $data['supportSettings']->token;
            /*
             * Загружаем файл
             */

            $host = $uploaddir . 'module.zip';
//            var_dump($host);
//            die();
			$arrContextOptions=array(
				"ssl"=>array(
					"verify_peer"=>false,
					"verify_peer_name"=>false,
				),
			);

            copy($url, $host, stream_context_create($arrContextOptions));

            if (!file_exists($host)) {
                echo json_encode(['error' => 1, 'data' => 'Файл не скачался']);
                exit();
            }
            $fileName = 'module.zip';
        }


        /*
         * Если не был загружен файл
         */
        if (empty($fileName)) {
            if (empty($data['file']['name'])) {
                echo json_encode(['error' => 1, 'data' => 'Выберите файл']);
                exit();
            }

            $fileNameArr = explode(".", (string)$data['file']['name']);
            $Extension = $fileNameArr[count($fileNameArr) - 1];

            if ($Extension != 'zip') {
                echo json_encode(['error' => 1, 'data' => 'Выберите файл .zip']);
                exit();
            }

            $uploadfile = $uploaddir . basename($data['file']['name']);
            if (!move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
                echo json_encode(['error' => 1, 'data' => 'Ошибка загрузки файла']);
                exit();
            }
            $fileName = $data['file']['name'];
        }

        $PathsArr = [];
        $zip = new \ZipArchive(); //Создаём объект для работы с ZIP-архивами
        //Открываем архив archive.zip и делаем проверку успешности открытия
        if ($zip->open($uploaddir . $fileName) === true) {

            /*
             * Собираем уведомление для поддтверждения
             */
            if(empty($zip->numFiles)){
                echo json_encode(['error' => 1, 'data' => 'Модуль не найден или архив пустой']);
                exit();
            }
            /*echo 123444;
            echo '<pre>';
            print_r($zip->numFiles);
            echo '</pre>';*/
            if ($action == 1) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $FileInfo = $zip->statIndex($i);

                    /*
                        * Проверим есть ли файл version.php
                    */
                    if (strpos($FileInfo['name'], 'version.php') !== false) {
                        $versionInfoStr = $zip->getFromIndex($FileInfo['index']);
                        $versionInfoStr = str_replace("<?php", "", $versionInfoStr);
                        $versionInfoStr = str_replace("<?", "", $versionInfoStr);
                        $versionInfoStr = str_replace("return", "", $versionInfoStr);

                        eval("\$versionInfoStr = $versionInfoStr;");
                        if (isset($versionInfoStr['version'])) {
                            $versionInfoArr = $versionInfoStr;
                        }
                    }
                    // Проверим есть ли такая папка или файл
                    $Pfile = str_replace('\\', '/', $DOCUMENT_ROOT_DIR . $FileInfo['name']);
                    $status = 'n';
                    $crc2 = '';
                    if (file_exists($Pfile)) {
                        $status = 'y';
                        $crc2 = hexdec(hash_file("crc32b", $Pfile));
                    }

                    $PathsArr[] = [
                        'path' => $FileInfo['name'],
                        'crc' => $FileInfo['crc'],
                        'crc2' => $crc2,
                        'status' => $status,
                    ];
                }
            }

            /*
             * Распаковываем
             */
            if ($action == 2) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $file = $zip->statIndex($i, \ZipArchive::FL_UNCHANGED);
                    $zip->renameIndex($i, str_replace("\\", '//', $file['name']));
                }
                $zip->close();


                if ($zip->open($uploaddir . $fileName) === true) {

                    $zip->extractTo($DOCUMENT_ROOT_DIR); //Извлекаем файлы в указанную директорию
                } else {
                    die("ERROR UNZIP");
                }

                $dirFiles = new \DirectoryIterator($DOCUMENT_ROOT_DIR);
                foreach ($dirFiles as $fileinfo) {
                    if ($fileinfo->isFile() && strpos($fileinfo->getFilename(), '\\') !== false) {

                        $source = $fileinfo->getPathname();
                        // Получим строку с правильным путем к файлу
                        $target = str_replace('\\', '/', $source);
                        // Создадим структуру каталогов для хранения нового файла
                        $dir = dirname($target);
                        if (!is_dir($dir)) {
                            mkdir($dir, 0755, true);
                        }

                        // Сравним CRC
                        $crc = hexdec(hash_file("crc32b", $source));
                        $crc2 = hexdec(hash_file("crc32b", $target));

                        echo '<p>' . $source . ' crc ' . $crc . '</p>';
                        echo '<p>' . $target . ' crc2 ' . $crc2 . '</p>';

                        // Переместим файл на правильный путь
                        rename($source, $target);
                    }
                }
            }
            $zip->close(); //Завершаем работу с архивом
        } else {
            echo json_encode(['error' => 1, 'data' => 'Архива не существует!']);
            exit();
        }

        /*echo '<pre>';
        print_r($versionInfoArr['ModuleInfo']['name']);
        echo '</pre>';
        exit;*/

        if ($action == 1) {
            /*
             * Проверим требуются ли дополнительный модули
             */
            if (empty($_POST['forcibly'])) {
                $requireModulesArr = [];
                if (isset($versionInfoArr['requireModules'])) {
                    if (is_array($versionInfoArr['requireModules'])) {
                        /*
                         * Проверим услановлены ли дополнительные модули
                         */
                        foreach ($versionInfoArr['requireModules'] as $requireModule) {
                            $checkModule = EximHelper::instance()->checkModule($requireModule['name_module'], $requireModule['version']);
                            if (!$checkModule) {
                                $requireModulesArr[] = $requireModule;
                            }
                        }
                    }
                }

                if (!empty($requireModulesArr)) {
                    $message = '<div class="alert alert-primary" role="alert">';
                    $message .= '<h3>Для установки '.$versionInfoArr['ModuleInfo']['name'].' v'.$versionInfoArr['version'].', трубуется установить дополнительные модули</h3>';
                    foreach ($requireModulesArr as $requireModuleArr) {
                        $message .= '<p><span style="color: red;">Требуется установить модуль ' . $requireModuleArr['name_module'] . ', версия ' . $requireModuleArr['version'] . '</span></p>';
                    }
                    foreach ($requireModulesArr as $requireModuleArr) {
                        $message .= '<p><a data-id="'.$requireModule['name_module'].'" class="btn btn-success installModule"
             data-install-url="'.EximHelper::instance()->getUrl().'/getmodule/'.$requireModule['name_module'].'/'.$requireModule['version'].'"
             href="#">Установить '.$requireModule['name_module'].' v'.$requireModule['version'].'</a></p>';
                    }
                    //$requireModule['name_module'] $requireModule['version']
                    $message .= '<p><a class="btn btn-info" href="/exim/listserver">Найти модули вручную</a></p>';
                    $message .= '<p><a data-id="'.$versionInfoArr['ModuleInfo']['name'].'" class="btn btn-danger installModule"
                data-forcibly="1" 
             data-install-url="'.EximHelper::instance()->getUrl().'/getmodule/'.$versionInfoArr['ModuleInfo']['name'].'/'.$versionInfoArr['version'].'"
             href="#">Установить '.$versionInfoArr['ModuleInfo']['name'].' v'.$versionInfoArr['version'].' принудительно</a></p>';
                    $message .= '</div>';
                    echo json_encode(['error' => 2, 'data' => 'Установите дополнительные модули', 'message' => $message]);
                    exit();
                }
            }

            $message = '<div class="alert alert-primary" role="alert">';
            $message .= '<h3>Действия над файлами</h3>';

            if (class_exists('modules\backups\models\mBackups')) {
                $message .= '<a href="#" class="btn btn-info btn-xs createBackup">Создать backup файлов и БД</a>';
            }

            $message .= '<input type="submit" data-action="2" name="submit" class="btn btn-success col-sm-12 eximFormSubmit" value="Установить" style="margin-top: 15px;">';

            /*echo '<pre>';
            print_r($PathsArr);
            echo '</pre>';*/

            foreach ($PathsArr as $PathArr) {
                if ($PathArr['status'] == 'n') {
                    $message .= '<br><span style="color: green;">' . $PathArr['path'] . ' - будет создан</span>';
                } else {
                    if ($PathArr['crc'] != $PathArr['crc2']) {
                        $message .= '<br><span style="color: red;">' . $PathArr['path'] . ' - будет перезаписан</span>';
                    } else {
                        $message .= '<br><span style="color: blue;">' . $PathArr['path'] . ' - уже такой существует</span>';
                    }

                    //$message .= '<br><span style="color: blue;">' . $PathArr['crc'] . ' - сумма; сумма текущего файла: ' . $PathArr['crc2'] . '</span>';
                }
            }
            $message .= '</div>';
            echo json_encode(['error' => 2, 'data' => 'Подтвердите действие', 'message' => $message]);
            exit();
        }

        if ($action == 2) {
            $message = '<div class="alert alert-primary" role="alert">';
            $message .= '<h3>Файлы успешно распакованы</h3>';
            $message .= '<h3>Выполняем миграции</h3>';
            //$message .= '<a class="btn btn-success col-sm-12" href="/migrations">Сканировать миграции</a>';
            $message .= '</div>';
            echo json_encode(['error' => 0, 'data' => 'Успешно', 'message' => $message]);
            exit();
        }

        echo json_encode(['error' => 1, 'data' => 'Ошибка']);
        exit();
    }

    /**
     * Загрузить локальный модуль на сервер
     * @param $nameModule
     */
    public function actionUpload($nameModule)
    {
        $Upload = EximHelper::instance()->Upload($nameModule);
        if ($Upload) {
            echo json_encode(['error' => 0, 'data' => 'Успешно']);
            exit();
        }

        echo json_encode(['error' => 1, 'data' => 'Ошибка']);
        exit();
    }

    /**
     * Првоерить модуль на сервере
     */
    public function actionCheckserver()
    {
        $data = $_POST;
        if (!isset($data['Modules'])) {
            return ['error' => 1, 'data' => 'Ошибка'];

        }
        $Modules = json_decode($data['Modules'], true);
        if (count($Modules)==0) {
			return "";
		}

        $ModulesArr = [];
        if (is_array($Modules)) {
            foreach ($Modules as $Module) {
                $ModulesArr[] = [
                    'version' => json_encode(EximHelper::instance()->getEximConfig($Module)),
                    'nameModule' => $Module,
                ];

            }
        }

        /*echo '<pre>';
        print_r($ModulesArr);
        echo '</pre>';
        exit;*/
        // echo EximHelper::instance()->url . '/checkmodules';

        $token = '';
        $data['supportSettings'] = Parameters::get('supportModules');
        if (isset($data['supportSettings']->token)) {
            $token = $data['supportSettings']->token;
        }

        $curl = new Curl();
		$curl->setOpt(CURLOPT_SSL_VERIFYPEER, FALSE);
        $curl->post(EximHelper::instance()->getUrl() . '/checkmodules', array(
            'token' => $token,
            'ModulesArr' => json_encode($ModulesArr),
        ));
        if ($curl->error) {

			return ['error' => 1, 'data' => 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n"];
        }
        //echo $curl->response;
        $dataChecktoken = json_decode($curl->response, true);
        if (isset($dataChecktoken['error']) && $dataChecktoken['error'] == 0) {
            echo $curl->response;
            return true;
        } else {
            echo $curl->response;
            exit;
        }
        $curl->close();

        return ['error' => 1, 'data' => 'Ошибка'];
    }

    /**
     * Экшен отдает архив с модулем
     * @param $nameModule
     * @throws \Exception
     */
    public function actionDownload($nameModule)
    {

        /*
         * Создадим папку если нет
         */
        $dir = __DIR__ . '/../ziptemp/';
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        /*
         * Создадим архив
         */
        $Destination = realpath(__DIR__ . '/../ziptemp') . DIRECTORY_SEPARATOR . $nameModule . '.zip';
        EximHelper::instance()->CreateZip($nameModule, $Destination);

        if (file_exists($Destination)) {
            header('Content-type: application/zip');
            header('Content-Disposition: attachment; filename="' . $nameModule . '.zip"');
            readfile($Destination);
            /*
             * удаляем zip
             */
            unlink($Destination);
        }

        exit;
    }

}