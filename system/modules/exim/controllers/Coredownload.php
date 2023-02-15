<?php

namespace modules\exim\controllers;

use core\Controller;
use core\FS;
use core\Html;
use core\Parameters;
use modules\exim\helpers\EximHelper;
use modules\exim\models\mModules;

class Coredownload extends Controller
{

    public function actionIndex()
    {
        $data = [];
        $data['supportSettings'] = Parameters::get('supportModules');

        /*
         * Получить текущий файл конфигурации version.php
         * Если есть валидный version.php то возьмем его
         */
        if ($EximCustom = EximHelper::instance()->getEximCustom('core1')) {
            if (EximHelper::instance()->checkEximValidate($EximCustom, true)) {
                $data['EximConfig'] = $EximCustom;
            }
        }

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
        Html::instance()->content = $this->render("/exim/CoreDownload.php", $data);
        Html::instance()->renderTemplate('@admin')->show();
    }

}