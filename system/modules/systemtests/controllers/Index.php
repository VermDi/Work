<?php
/**
 * Created by PhpStorm.
 * User: dulentcov-smishko
 * Date: 13.09.2018
 * Time: 16:00
 */

namespace modules\systemtests\controllers;


use core\Controller;
use core\FS;


class Index extends Controller
{
    public function actionIndex()
    {
        /*
         * Смотрим все папочки с модулями
         */

        $modules_folder = __DIR__ . '/../../';
        $folders = FS::instance()->getFoldersInFolder($modules_folder);
        /*
         * Смотрим а есть ли у модуля папочка tests
         */
        foreach ($folders as $folder) {
            $real_folder = $modules_folder . "/" . $folder . "/tests";
            if (file_exists($real_folder)) {
                /*
                 * Если папочка есть, то смотрим а есть ли в ней файлы тестов
                 */
                $files = FS::instance()->getFilesInFolder($real_folder, 'php');
                if ($files) {
                    foreach ($files as $k => $v) {
                        $way = "\\modules\\" . $folder . "\\tests\\" . substr($v, 0, -4);
                        echo "<hr><b>Модуль: " . $folder . " &rarr; Класс " . substr($v, 0, -4) . " &rarr; </b>";
                        if (class_exists($way)) {
                            $class = new $way();
                            if (method_exists($class, 'run')) {
                                $err = '<b style="color: green;">OK</b>';
                                try {
                                    $result = $class->run();
                                } catch (\Exception $e) {
                                    $err = '  &rarr; <b style="color: red;">BAD</b>';
                                    echo "<b>" . $e->getMessage() . "</b>";

                                }
                                echo $err;
                            } else {
                                echo " <b> метод run не найден</b><br>";
                            }
                        } else {
                            echo " <b> класс отсутсвует</b><br>";
                        }
                        unset($class);
                    }
                }
            } else {
                // echo "<p>Модуль <b>" . $folder . "</b> не содержит тестов.</p>";
            }
        }


    }

    public function actionStart()
    {

    }

    public function actionStats()
    {

    }


}