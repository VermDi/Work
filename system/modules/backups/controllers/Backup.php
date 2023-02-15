<?php

namespace modules\backups\controllers;

use core\App;
use core\Controller;
use core\Html;
use modules\backups\models\mBackups;
use modules\backups\services\sBackups;
use modules\user\models\USER;

class Backup extends Controller
{

    public function actionIndex()
    {
        header('Location: /backups/backup/list');
    }

    public function actionList($status = 0)
    {
        $data = [];
        $data['status'] = $status;

        Html::instance()->setJs("/assets/vendors/datatables/js/jquery.dataTables.min.js");
        Html::instance()->setCss("/assets/vendors/datatables/css/jquery.dataTables.min.css");

        Html::instance()->setCss('/assets/vendors/jquery.datetimepicker/jquery.datetimepicker.css');
        Html::instance()->setJs('/assets/vendors/jquery.datetimepicker/jquery.datetimepicker.js');

        Html::instance()->setCss('/assets/vendors/toastr-master/toastr.css');
        Html::instance()->setJs('/assets/vendors/toastr-master/toastr.js');

        Html::instance()->setJs("/assets/modules/backups/js/backup.js");

        Html::instance()->content = $this->render("/backups/List.php", $data);
        Html::instance()->renderTemplate('@admin')->show();
    }

    public function actionSave()
    {
        $data = $_REQUEST;
        if(!isset($data['comments'])){
            echo json_encode(['error' => 1, 'data' => 'Введите комментарий']);
            exit();
        }

        $BackupsData=[];
        if(!empty($data['id'])){
            $BackupsData['id'] = $data['id'];
        } else {
            $BackupsData['date'] = date('Y-m-d H:i:s');
        }

        $BackupsData['comments'] = $data['comments'];

        if(!empty($data['db'])){
            $BackupsData['file_db'] = sBackups::instance()->backupDB();
        }
        if(!empty($data['files'])){
            $excludeFolder=sBackups::instance()->getExcludeFolder($data['exclude_folder']);
            $BackupsData['file_files'] = sBackups::instance()->backupFiles($excludeFolder);
        }


        $idBackup = mBackups::instance()->saveBackups($BackupsData);
        if(!empty($idBackup)){
            $dataGetlist = $this->actionGetlist(false, $idBackup, true);
            if(isset($dataGetlist[0])){
                echo json_encode(['error' => 0, 'data' => 'Успешно', 'jsonRow' => json_encode($dataGetlist[0])]);
            }
        }
        exit();
    }

    public function actionRestorefiles()
    {
        $data = $_REQUEST;

        if(empty($data['files'])){
            echo json_encode(['error' => 1, 'data' => 'Пустой files']);
            exit();
        }
        $BackupFile = sBackups::instance()->ROOT.'/'. sBackups::instance()->RootFolderName.$data['files'];
        if(!file_exists($BackupFile)){
            echo json_encode(['error' => 1, 'data' => 'Файл бэкапа не найден']);
            exit();
        }

        sBackups::instance()->RestoreFiles($BackupFile);

        echo json_encode(['error' => 0, 'data' => 'Успешно']);
        exit;
    }

    public function actionRestoredb()
    {
        $data = $_REQUEST;

        if(empty($data['dbfile'])){
            echo json_encode(['error' => 1, 'data' => 'Пустой dbfile']);
            exit();
        }
        $BackupFile = sBackups::instance()->ROOT.'/'. sBackups::instance()->RootFolderName.$data['dbfile'];
        if(!file_exists($BackupFile)){
            echo json_encode(['error' => 1, 'data' => 'Файл бэкапа не найден']);
            exit();
        }

        if(sBackups::instance()->RestoreDB($BackupFile)){
            echo json_encode(['error' => 0, 'data' => 'Успешно']);
            exit;
        } else {
            echo json_encode(['error' => 1, 'data' => 'Ошибки']);
            exit;
        }


    }

    public function actionGetlist($status = false, $id = false, $getData = false)
    {
        /*
        * для передачи по ajax в формет json
        */
        $options=[];
        if(isset($_GET['start'])){
            $options['offset'] = $_GET['start'];
        }
        if(isset($_GET['length'])){
            $options['limit'] = $_GET['length'];
        }
        if(!empty($id)){
            $options['id'] = $id;
        }
        //$options['offset'] = 0;
        //$options['limit'] = 5;
        $res = mBackups::instance()->getBackups($options);

        $countAll = mBackups::instance()->getBackups(['getCount'=>true, 'status'=>$status]);
        //$TypesArr = mAboutProject::instance()->getTypesArr();

        if(!empty($res)){
            foreach ($res as $k => $v) {
                if(empty($v->file_db)){
                    $res[$k]->file_db = 'Нет копии БД';
                } else {
                    $res[$k]->file_db = '<a class="btn btn-xs btn-info" href="'.$v->file_db.'">Скачать</a> <a class="btn btn-xs btn-info BackupDbRestore" href="#" data-dbfile="'.$v->file_db.'">Восстановить</a>';
                }
                if(empty($v->file_files)){
                    $res[$k]->file_files = 'Нет копии файлов';
                } else {
                    $res[$k]->file_files = '<a class="btn btn-xs btn-info" href="'.$v->file_files.'">Скачать .zip</a> <a class="btn btn-xs btn-info BackupRestore" href="#" data-files="'.$v->file_files.'">Восстановить</a>';
                }
                $res[$k]->control = "<!--<a data-title='Редактирование' href='/backups/backup/form/" . $v->id . "/ajax' class='btn btn-warning btn-xs ajax'><i class='fa fa-edit'></i></a>-->
                                <a data-id='" . $v->id . "' class='btn btn-danger btn-xs delProject'><i class='fa fa-trash-o'></i></a>";
            }
        } else {
            $res = [];
        }
        /*echo '<pre>';
        print_r($res);
        echo '</pre>';
        exit;*/

        if(!empty($getData)){
            return $res;
        } else {
            return (['data'=>$res, 'recordsTotal'=>$countAll, 'recordsFiltered'=>$countAll]);
        }
        die();
    }

    public function actionForm($id = false, $ajax = false)
    {
        if ($id == 0) {
            $id = false;
        }
        $data['model'] = mBackups::instance()->factory($id);
        echo $this->render("/backups/Form.php", $data);
        die();
    }

    public function actionDel($id = false)
    {
        $Backups = mBackups::instance()->getBackups(['id'=>$id,'getOne'=>true]);
        if(empty($Backups->id)){
            echo json_encode(['error' => 1, 'data' => 'Не найден']);
            exit();
        }

        // Удалим файлы
        if(!empty($Backups->file_files)){
            $BackupFile = sBackups::instance()->ROOT.'/'. sBackups::instance()->RootFolderName.$Backups->file_files;
            if(file_exists($BackupFile)){
                unlink($BackupFile);
            }
        }

        // Удалим БД
        if(!empty($Backups->file_db)){
            $BackupFile = sBackups::instance()->ROOT.'/'. sBackups::instance()->RootFolderName.$Backups->file_db;
            if(file_exists($BackupFile)){
                unlink($BackupFile);
            }
        }


        mBackups::instance()->delete($id);

        echo json_encode(['error' => 0, 'data' => 'Успешно']);
        exit();
    }

}