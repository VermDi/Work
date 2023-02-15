<?php

namespace modules\exim\models;

use core\FS;
use core\Model;
use modules\exim\helpers\EximHelper;

/**
 * Class mModules
 * @property int id -
 * @property string payment_one -
 * @property string payment_two -
 * @property string percent -
 */
class mModules
{
    /**
     * Инстантс
     * @return $this
     */
    protected static $instance;

    public static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Получает перечень папок модулей
     * @param bool $options
     * @return array
     */
    public function getModules($options = false)
    {
        $getFolder = FS::instance()->getFoldersInFolder(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR);
        $ModulesArr = [];
        foreach ($getFolder as $nameModule) {
            $Module = [];
            $Module['id'] = $nameModule;
            $Module['file_exim'] = $this->ExistFileExim($nameModule);
            $Module['exim_info'] = EximHelper::instance()->getEximConfig($nameModule);
            $ModulesArr[] = $Module;
        }
        return $ModulesArr;
    }

    public function ExistFileExim($nameModule)
    {
        $EximConfig = 'n';
        if ($EximCustom = EximHelper::instance()->getEximCustom($nameModule)) {
            $EximConfig = 'y';
        }
        return $EximConfig;
    }

    public function saveModules($data)
    {

    }


    public function checkModules($data)
    {

    }


}