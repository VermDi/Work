<?php
/**
 * Create by e-Mind Studio
 * User: Женя
 * Date: 08.04.2017
 * Time: 18:35
 */

namespace core;
/**
 * Класс для кэширования картинок в нужном размере
 *
 * Class Imgcache
 * @package core
 */
class Imgcache
{
    public static $error;

    /**
     * Возвращает кэш картинки по пути, если кэша нет, создает новый кэш
     * @param $key - ключ
     * @param $name - путь до картинки
     * @param bool $width - ширина
     * @param bool $height - высота
     * @param bool $label - наносить логотип ?
     * @return string
     */
    public static function getImg($key, $name, $width = false, $height = false, $label = false)
    {
        if ($label == false) {
        }
        /*
         * Не переданые высота и ширина, возвращаем ориинал
         */
        if ($width == false and $height == false) {
            return $name;
        }
        /*
         * Если переданный файл оригинал не существует, то возвращаем false.
         */
        if (!file_exists($name)) {
            return false;
        }
        /*
         * Если переданный файл не файл, то возвращаем false.
         */
        if (!is_file($name)) {
            return false;
        }

        $partWay = "/public/cache/" . $key . "/" . $width . "x" . $height . "/" . basename($name);
        $way = $_SERVER['DOCUMENT_ROOT'] . $partWay;
        if (file_exists($way)) { ///если файл есть, то возвращаем его
            return $partWay;
        } else {
            //если нет генерируем новый
            return self::setImg($key, $name, $width, $height);
        }
    }

    /**
     * Создает кэш картинки в нужно размере
     * @param $key - ключ
     * @param $name - путь до картинки
     * @param bool $width - ширина
     * @param bool $height - высота
     * @param bool $label - наносить логотип ?
     * @return string
     */
    public static function setImg($key, $name, $width = false, $height = false, $label = false)
    {
        if ($label == false) {
        }
        $partWay = "/public/cache/" . $key . "/" . $width . "x" . $height . "/" . basename($name);

        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . "/public/cache/" . $key . "/" . $width . "x" . $height . "/")) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . "/public/cache/" . $key . "/" . $width . "x" . $height . "/", 0755, true);
        }
        $handle = new \upload($name);
        $handle->image_resize = true; //масштабировать
        $handle->image_ratio = true; //соблюдать пропорции
        if (!empty($width)) { //ширина
            $handle->image_x = $width;
        }
        if (!empty($height)) { //высота
            $handle->image_y = $height;
        }
        //новое имя файла
        $handle->file_new_name_body = substr(basename($name), 0, strrpos(basename($name), "."));
        //сохраням
        $handle->process($_SERVER['DOCUMENT_ROOT'] . "/public/cache/" . $key . "/" . $width . "x" . $height . "/");
        //если все ок вовзвращаем имя
        if (!$handle->processed) {
            //инача даем ошибку
            self::$error = 'error : ' . $handle->error;
            return false;
        }
        return $partWay;
    }

    /**
     * Удаляет кэш по ключу, если переданые ширина и высота, удалит кэш только конкрентного размера. Иначе целиком по ключу.
     * @param $key - ключ
     * @param $width -ширина
     * @param $height - высота
     * @return bool
     */
    public static function clearCache($key, $width = false, $height = false)
    {
        if (empty($key)) {
            self::$error = "Не передан ключ";
            return false;
        }
        $partWay = "/public/cache/" . $key . "/";
        if ($width == false and $height == false) {
            return FS::instance()->removeFolder($_SERVER['DOCUMENT_ROOT'] . $partWay, true);
        } else {
            return FS::instance()->removeFolder($_SERVER['DOCUMENT_ROOT'] . "/public/cache/" . $key . "/" . $width . "x" . $height . "/", true);
        }

    }

    /**
     * Сложная функция, так как она должна удалить кэш картинки по ключу во всех папках и размерах
     * @param $key
     * @param $img
     */
    public static function clearImgCache($key, $img)
    {
        $partWay = $_SERVER['DOCUMENT_ROOT'] . "/public/cache/" . $key . "/";
        $folders = FS::instance()->getFoldersInFolder($partWay);
        foreach ($folders as $folder) {
            $file = $partWay . $folder . "/" . $img;
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }
}