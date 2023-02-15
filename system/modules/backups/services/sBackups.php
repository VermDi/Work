<?php

namespace modules\backups\services;

use modules\backups\models\mBackups;

class sBackups
{
    protected static $instance;

    public $ROOT = __DIR__ . '/../../../..';
    public $RootFolderName = 'www';
    public $dirBackup = '/public/backups/';

    public static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function backupDB()
    {
        $time = time();
        $backupName = $time . md5($time) . '.sql';
        $dirBac = $this->ROOT . '/' . $this->RootFolderName . $this->dirBackup;

        $Destination = realpath($dirBac) . DIRECTORY_SEPARATOR . $backupName;
        $Destination = str_replace(DIRECTORY_SEPARATOR, '/', $Destination);

        try {
            $dump = new sBackupDbMySQL('mysql:host=' . _DB_SERVER_ . ';dbname=' . _DB_NAME_ . '', _DB_USER_, _DB_PASS_);
            $dump->start($Destination);
        } catch (\Exception $e) {
            //echo 'Ошибка при создании бэкапа: '.$e->getMessage();
        }
        if (!file_exists($Destination)) {
            return '';
        }

        return $this->dirBackup . $backupName;
    }

    public function backupFiles($ExcludeFiles)
    {

        $time = time();
        $backupName = $time . md5($time) . '.zip';

        $dirBac = $this->ROOT . '/' . $this->RootFolderName . $this->dirBackup;

        if (!file_exists($dirBac)) {
            mkdir($dirBac, 0755, true);
        }
        $Destination = realpath($dirBac) . DIRECTORY_SEPARATOR . $backupName;
        $Destination = str_replace(DIRECTORY_SEPARATOR, '/', $Destination);
        //echo $Destination;
        //exit;

        $ziper = new sZipper();
        $ziper->make($Destination)
            ->setExcludeFiles($ExcludeFiles)
            ->add($this->ROOT)
            ->end();


        return $this->dirBackup . $backupName;
    }

    public function RestoreFiles($BackupFile)
    {
        $ziper = new sZipper();
        $ziper->unzip($BackupFile, $this->ROOT . '/../', true);
    }

    public function RestoreDB($BackupFile)
    {
        // Сохраним таблицу с бекапами
        $Backups = mBackups::instance()->getBackups();
        $Backups = json_decode(json_encode($Backups), true);

        $db = _DB_NAME_;
        $dump = new sBackupDbMySQL('mysql:host='._DB_SERVER_.';dbname='.$db.'', _DB_USER_, _DB_PASS_);
        $res = $dump->restore($BackupFile);

        // Запишем таблицу с бекапами
        foreach ($Backups as $Backup){
            mBackups::instance()->saveBackups($Backup);
        }

        return $res;
    }


    public function getExcludeFolder($exclude_folder)
    {
        $excludeFolder = [];
        if (!empty($exclude_folder)) {
            $exclude_folderArr = explode(',', $exclude_folder);
            if (!empty($exclude_folderArr)) {
                foreach ($exclude_folderArr as $exclude_folderRow) {
                    $exclude_folderRow = trim($exclude_folderRow);
                    $lastSim = substr($exclude_folderRow, -1);
                    if ($lastSim == '/') {
                        $exclude_folderRow = mb_substr($exclude_folderRow, 0, -1);
                    }
                    $excludeFolder[] = $exclude_folderRow;
                }
            }

        }
        return $excludeFolder;
    }


}