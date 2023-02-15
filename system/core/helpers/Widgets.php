<?php
/**
 * Create by e-Mind Studio
 * User: dulentcov-smishko
 * Date: 18.03.2019
 * Time: 18:53
 */

namespace core\helpers;


use core\FS;
use core\Widget;

class Widgets
{
	/**
	 * На вход принимает начало пути
	 *
	 * @param $startPath
	 *
	 * После чего доводит строку до 6 символов, затем разбивает по 2 символа и создает новые путь
	 * вида xx/xx/xx, такой вид обеспечивает нормальную генерацию папок и отсутствие огромного скопления элементов
	 * в одной папке
	 * Как следствие чтение с диска идет быстрее, нежели в одной папке есть 10 000 элементов
	 *
	 * @return string
	 */
	public static function folderPath($startPath){
		$idPath = str_pad($startPath, 6, "0"); //дополнми строку до 6 символов и разобьем... по 2 символа на путь
		$idPath = str_split($idPath, 2);
		return implode("/", $idPath);

	}
    public static function getAll()
    {
        /**
         * Получили имена папок модулей
         */
        $widget = [];
        $path = __DIR__ . "/../../modules/";
        $folders = FS::instance()->getFoldersInFolder(__DIR__ . "/../../modules/");
        $i = 0;
        /**
         * проверим наличие файлов
         */
        foreach ($folders as $folder) {
            if (file_exists($path . $folder . "/widgets")) {
                $w = FS::instance()->getFilesInFolder($path . $folder . "/widgets");
                if (is_array($w)) {
                    foreach ($w as $file) {
                        /**
                         * Проверим существование класса
                         */
                        if (class_exists('\modules\\' . $folder . '\widgets\\' . basename($file, ".php"), true)) {
                            $clp = '\modules\\' . $folder . '\widgets\\' . basename($file, ".php");
                            $cl = new $clp();
                            /**
                             * Проверим что это не внешний класс, который не связан с виджетами
                             */
                            if (method_exists($cl, 'possibilityList')) {

                                $i++;
                                $widget[$i]['module'] = $folder;
                                $widget[$i]['class'] = basename($file, ".php");
                                $widget[$i]['methods'] = $cl::possibilityList();


                                /**
                                 * Имя виджета для красивого отображения
                                 */
                                if (isset($cl->name)) {
                                    $widget[$i]['name'] = $cl->name;
                                }
                            }

                        }
                    }
                }

            }
        }
        return $widget;

    }

}