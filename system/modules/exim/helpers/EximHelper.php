<?php

namespace modules\exim\helpers;

use core\Controller;
use core\FS;
use core\helpers\Zipper;
use core\Parameters;
use Curl\Curl;
use modules\exim\models\mModules;

class EximHelper
{

    public $urlArr = [
        //'http://mind-cms2/eximserver/api',
        'https://mind-cms.ru/eximserver/api',
        'https://mind-cms.com/eximserver/api',
        'http://mind-cms.ru/eximserver/api',
        'http://mind-cms.com/eximserver/api',
    ];
    public $curl;

    protected static $instance;

    public static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function getUrl()
    {
        foreach ($this->urlArr as $url) {
            $content = EximHelper::instance()->getUrlBuffer($url);
            if ($content == 1) {
                return $url;
            }
        }
        return '';
    }

    public function getUrlBuffer($url)
    {
		$arrContextOptions=array(
			"ssl"=>array(
				"verify_peer"=>false,
				"verify_peer_name"=>false,
			),
		);
		ob_start();                                //  Let's start output buffering.
        echo file_get_contents($url, false, stream_context_create($arrContextOptions));
        $contents = ob_get_contents();             //  Instead, output above is saved to $contents
        ob_end_clean();
        return $contents;
    }

    /**
     * Получить текущий файл конфигурации version.php, если нет то будет по умолчанию
     * @param $nameModule
     * @return array|bool
     */
    public function getEximConfig($nameModule)
    {
        $EximConfig = EximHelper::instance()->getEximDefault($nameModule);
        EximHelper::instance()->checkEximValidate($EximConfig);
        /*
         * Если есть валидный в модуле version.php то возьмем его
         */
        if ($EximCustom = EximHelper::instance()->getEximCustom($nameModule)) {
            if (EximHelper::instance()->checkEximValidate($EximCustom, true)) {
                $EximConfig = $EximCustom;
            }
        }

        return $EximConfig;
    }

    /**
     * Получить README.md модуля
     * @param $nameModule
     * @return false|string
     */
    public function getReadme($nameModule)
    {
        $d = DIRECTORY_SEPARATOR;
        $ReadmeText = '';
        $ReadmeModule = __DIR__ . $d . '..' . $d . '..' . $d . '..' . $d . 'modules' . $d . $nameModule . $d . 'README.md';
        if (file_exists($ReadmeModule) && is_file($ReadmeModule)) {
            $ReadmeText = file_get_contents($ReadmeModule);
        }
        return $ReadmeText;
    }

    /**
     * Метод проверяет наличие файла версий, и возвращает array or BOOL FALSE
     * @param $nameModule
     * @return bool|array
     */
    public function getEximCustom($nameModule)
    {
        $d = DIRECTORY_SEPARATOR;
        $EximConfig = false;
        if ($nameModule == 'core1') {
            $exim_module = __DIR__ . $d . '..' . $d . '..' . $d . '..' . $d . 'core' . $d . 'version.php';
        } else {
            $exim_module = __DIR__ . $d . '..' . $d . '..' . $d . '..' . $d . 'modules' . $d . $nameModule . $d . 'version.php';
        }
        if (file_exists($exim_module) and is_readable($exim_module) && $this->checkFileSyntaxError($exim_module)) {
            $EximConfig = include($exim_module);
        }

        if (!EximHelper::instance()->checkEximValidate($EximConfig, true)) {
            return false;
        }

        return $EximConfig;
    }

    /**
     * Проверим файл на синтаксические ошибки
     * @param $filename
     * @return bool
     */
    public function checkFileSyntaxError($filename)
    {
        ob_start();
        $output = shell_exec('php -l ' . $filename);
        echo $output;
        $contents = ob_get_contents();
        ob_end_clean();

        if (strpos($contents, 'No syntax errors') !== false) {
            return true;
        }
        return false;
    }


    /**
     * Создадим символическую ссылку
     * @param $folder1
     * @param $folder2
     * @return bool
     */
    public function CreateLink($folder1, $folder2)
    {
        ob_start();
        $info = shell_exec('ln -s ' . $folder1 . ' ' . $folder2);
        echo $info;
        $info = shell_exec('mklink /D ' . $folder1 . ' ' . $folder2);
        echo $info;
        $contents = ob_get_contents();
        ob_end_clean();
        return true;
    }

    /**
     * Возвращает базовый настроечный массив
     * @param $nameModule
     * @return array
     */
    public function getEximDefault($nameModule)
    {
        $data['nameModule'] = $nameModule;
        $DefaultVersion = include __DIR__ . '/../templates/exim/DefaultVersion.php';
        return $DefaultVersion;
    }

    /**
     * Валидатор Конфига
     * @param $EximConfig
     * @return bool
     */
    public function checkEximValidate($EximConfig, $getResult = false)
    {
        if (!is_array($EximConfig)) {
            if ($getResult) {
                return false;
            } else {
                echo 'Ошибка в version.php - Не массив';
                exit;
            }
        }
        if (!isset($EximConfig['version'])) {
            if ($getResult) {
                return false;
            } else {
                echo 'Ошибка в version.php - Версия не найдена';
                exit;
            }
        }
        /*if ($EximConfig['version'] != '1.0') {
        if($getResult){ return false; } else {
            echo 'Ошибка в version.php - Версия не поддерживаеся';
            exit;
        }
        }*/
        if (!isset($EximConfig['ModuleInfo']['name'])) {
            if ($getResult) {
                return false;
            } else {
                echo 'Ошибка в version.php - ModuleInfo name не найден';
                exit;
            }
        }
        if (!isset($EximConfig['ModuleInfo']['link_home'])) {
            if ($getResult) {
                return false;
            } else {
                echo 'Ошибка в version.php - ModuleInfo link_home не найден';
                exit;
            }
        }
        if (empty($EximConfig['Folders'])) {
            if ($getResult) {
                return false;
            } else {
                echo 'Ошибка в version.php - Folders пустой';
                exit;
            }
        }
        return true;
    }

    /**
     * Првоерить токен на сервере
     * @param $parameters
     * @return bool
     */
    public function checkToken($parameters)
    {
        $curl = new Curl();
        if (empty($parameters['token'])) {
            return false;
        }
		$curl->setOpt(CURLOPT_SSL_VERIFYPEER, FALSE);
        $curl->post(EximHelper::instance()->getUrl() . '/checktoken', $parameters);
        if ($curl->error) {
            //echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
            return false;
        }

        $dataChecktoken = json_decode($curl->response, true);
        if (isset($dataChecktoken['error']) && $dataChecktoken['error'] == 0) {
            /*if(isset($dataChecktoken['tokenInfo']) && is_string($dataChecktoken['tokenInfo'])){
                return json_decode($dataChecktoken['tokenInfo'], true);
            }*/
            return true;
        }

        $curl->close();
    }


    /**
     * Проверить текущий токен,
     * @param $parameters
     * @return bool
     */
    public function checkCurrentToken($parameters)
    {
        $data['supportSettings'] = Parameters::get('supportModules');
        if (!isset($data['supportSettings']->token)) {
            return false;
        }
        $parameters['token'] = $data['supportSettings']->token;
        $checkToken = $this->checkToken($parameters);
        return $checkToken;
    }

    /**
     * Получить список модулей с сервера
     * @return bool
     */
    public function Getlistserver($getCore = false)
    {
        $data['supportSettings'] = Parameters::get('supportModules');
        if (!isset($data['supportSettings']->token)) {
            return false;
        }

        $modulesArr = mModules::instance()->getModules();

        $curl = new Curl();
		$curl->setOpt(CURLOPT_SSL_VERIFYPEER, FALSE);
        $curl->post(EximHelper::instance()->getUrl() . '/getlist', array(
            'token' => $data['supportSettings']->token,
            'modulesArr' => $modulesArr,
        ));
        if ($curl->error) {
            //echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
            return false;
        }

        $dataChecktoken = json_decode($curl->response, true);
        if (isset($dataChecktoken['error']) && $dataChecktoken['error'] == 0) {
            if (is_string($dataChecktoken['jsonData'])) {
                $modulesData = json_decode($dataChecktoken['jsonData'], true);
                /*echo '<pre>';
                print_r($modulesData);
                echo '</pre>';*/
                if (isset($modulesData['data'])) {
                    if (is_array($modulesData['data'])) {
                        $modulesDataNew = [];
                        foreach ($modulesData['data'] as $modulesDataRow) {
                            $modulesDataRow['readme'] = nl2br($modulesDataRow['readme']);
                            /*echo '<pre>';
                            print_r($modulesDataRow);
                            echo '</pre>';*/
                            if (!$getCore) {
                                if ($modulesDataRow['id'] != 'core1') {
                                    $modulesDataNew[] = $modulesDataRow;
                                }
                            } else {
                                if ($modulesDataRow['id'] == 'core1') {
                                    $modulesDataNew[] = $modulesDataRow;
                                }
                            }
                        }
                        return json_encode(['data' => $modulesDataNew, 'recordsTotal' => $modulesData['recordsTotal'], 'recordsFiltered' => $modulesData['recordsFiltered']]);
                    }
                }
            }

            //return $dataChecktoken['jsonData'];
        }

        $curl->close();
        return false;
    }

    /**
     * Проверяем модуль на сервере
     * @param $nameModule
     * @return bool
     */
    public function Checkserver($nameModule)
    {
        /*$data['supportSettings'] = Parameters::get('supportModules');
        if (!isset($data['supportSettings']->token)) {
            return false;
        }*/

        $curl = new Curl();
		$curl->setOpt(CURLOPT_SSL_VERIFYPEER, FALSE);
        $curl->post(EximHelper::instance()->getUrl() . '/checkmodule', array(
            //'token' => $data['supportSettings']->token,
            'version' => json_encode(EximHelper::instance()->getEximConfig($nameModule)),
            'nameModule' => $nameModule,
        ));
        if ($curl->error) {
            //echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
            return false;
        }
        //echo $curl->response;
        $dataChecktoken = json_decode($curl->response, true);
        if (isset($dataChecktoken['error']) && $dataChecktoken['error'] == 0) {
            return true;
        } else {
            echo $curl->response;
            exit;
        }

        $curl->close();
    }

    /**
     * Загружаем локальный модуль на сервер
     * @param $nameModule
     * @return bool
     */
    public function Upload($nameModule)
    {
        $data['supportSettings'] = Parameters::get('supportModules');
        if (!isset($data['supportSettings']->token)) {
            return false;
        }

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

        if (!file_exists($Destination)) {
            return false;
        }

        $curl = new Curl();
		$curl->setOpt(CURLOPT_SSL_VERIFYPEER, FALSE);
        $curl->post(EximHelper::instance()->getUrl() . '/download', array(
            'token' => $data['supportSettings']->token,
            'zip' => base64_encode(file_get_contents($Destination)),
            'version' => json_encode(EximHelper::instance()->getEximConfig($nameModule)),
            'nameModule' => $nameModule,
            'readme' => base64_encode($this->getReadme($nameModule)),
        ));
        if ($curl->error) {
            //echo 'Error: ' . $curl->errorCode . ': ' . $curl->errorMessage . "\n";
            return false;
        }
        //echo $curl->response;
        $dataChecktoken = json_decode($curl->response, true);
        if (isset($dataChecktoken['error']) && $dataChecktoken['error'] == 0) {
            return true;
        } else {
            echo $curl->response;
            exit;
        }

        $curl->close();
    }

    /**
     * Проверить установлен ли модуль с данной версией
     * @param $nameModule
     * @param $version
     * @return bool
     */
    public function checkModule($nameModule, $version)
    {
        $EximConfig = EximHelper::instance()->getEximConfig($nameModule);
        // Если есть знак >
        $version2 = str_replace(">", "", $version);
        if($version2!=$version){
            // То смотрим есть такая версия или больше
            if ($EximConfig['version'] >= $version2) {
                return true;
            }
        }
        /*
         * Смотрим текущую версию модуля
         */
        if ($EximConfig['version'] == $version) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * Создаем архив модуля
     * @param $nameModule
     * @param $Destination
     * @throws \Exception
     */
    public function CreateZip($nameModule, $Destination)
    {
        $ROOT_SITE = realpath(__DIR__ . "/../../../../") . DIRECTORY_SEPARATOR;
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
            if (file_exists($ROOT_SITE . $Folder)) {
                $Folders[] = $Folder;
            }
        }
        if (empty($Folders)) {
            throw  new \Exception('Папки не найдены');
            exit;
        }
        /*
         * Запаковываем файлы
         */
        $zip = new Zipper();
        $zip->make($Destination);
        foreach ($Folders as $Folder) {
            $FolderN = substr($Folder, 0, strrpos($Folder, "/"));
            $FolderN = str_replace('\\', DIRECTORY_SEPARATOR, $FolderN);
            $FolderN = str_replace('/', DIRECTORY_SEPARATOR, $FolderN);

            $zip->in($FolderN); //подпапка, по сути без последнего кусока урла /system/ddd/dd -> system/ddd
            $FolderName = str_replace('\\', DIRECTORY_SEPARATOR, $ROOT_SITE . $Folder);
            $FolderName = str_replace('/', DIRECTORY_SEPARATOR, $FolderName);
            $zip->add($FolderName);
        }
        $zip->end();
        /*
         * отдаём файл на скачивание
         */
        if (!file_exists($Destination)) {
            throw  new \Exception('Архив не найден');
            exit;
        }
    }

    /**
     * Получаем массив в виде кода для сохранения php в файл
     * @param bool $array
     * @return string
     */
    public function ArrayExport($array = false)
    {
        ob_start();                                //  Let's start output buffering.
        echo "<?php \n return ";
        var_export($array);
        echo ";";
        $contents = ob_get_contents();             //  Instead, output above is saved to $contents
        ob_end_clean();
        return $contents;
    }

    /**
     * Вернет www или public_html и т.д.
     * @return mixed|string
     */
    public function getRootFolder()
    {
        $getNameRootFolder = '';
        if (isset($_SERVER['DOCUMENT_ROOT'])) {
            $DirArr = explode("/", $_SERVER['DOCUMENT_ROOT']);
            $getNameRootFolder = array_pop($DirArr);
        }
        return $getNameRootFolder;
    }

    /**
     * Вернет путь до папки system и www
     * @return mixed|string
     */
    public function getPathRootFolder()
    {
        $getNameRootFolder = EximHelper::instance()->getRootFolder();
        return str_replace($getNameRootFolder . 'test123776', '', $_SERVER['DOCUMENT_ROOT'] . 'test123776');
    }

    /**
     * @return bool
     */
    public function checkWWW()
    {
        $getNameRootFolder = EximHelper::instance()->getRootFolder();
        $filename = __DIR__ . '/../../../../www/';
        if (file_exists($filename)) {
            return true;
        }
        return false;
    }

}