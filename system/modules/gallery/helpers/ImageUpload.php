<?php
/**
 * Created by PhpStorm.
 * User: Евгения
 * Date: 07.09.2017
 * Time: 13:53
 */

namespace modules\gallery\helpers;


use core\Tools;

class ImageUpload
{
    /*
     * Мой комментарий.
     */
    const MAX_SIZE = 2000;
    public static $valid_formats = ["jpg", "png", "gif", "bmp", "jpeg"];
    public static $uploaddir = "/public/images/";
    public $image_name = array();
    public $error;
    public static $folder_name = "";
    CONST MAX_RESOLUTION = 1900;

    public function __construct($folder_name = false)
    {

        self::$folder_name = $folder_name;
        $this->createUploadDir();
    }

    public static function getExtension($str)
    {
        $i = strrpos($str, ".");
        if (!$i) {
            return "";
        }
        $l = strlen($str) - $i;

        $ext = substr($str, $i + 1, $l);

        return $ext;
    }

    function createUploadDir()
    {
        if (!empty(self::$folder_name)) {
            self::$uploaddir .= self::$folder_name . "/";
        }
        if (!file_exists(_ROOT_PATH_ . self::$uploaddir)) {
            mkdir(_ROOT_PATH_ . self::$uploaddir, 0777, true);
        }
    }

    function run()
    {
        foreach ($_FILES['photos']['name'] as $name => $value) {
            $filename = stripslashes($_FILES['photos']['name'][$name]);
            $size     = filesize($_FILES['photos']['tmp_name'][$name]);
            $ext      = self::getExtension($filename);
            $ext      = strtolower($ext);
            if (in_array($ext, self::$valid_formats)) {
                if ($size < (self::MAX_SIZE * 1024)) {
                    $image_name = Tools::generateRandomString() . "." . $ext;
                    $newname    = _ROOT_PATH_ . self::$uploaddir . $image_name;
                    $image      = new SimpleImage();
                    $image->load($_FILES['photos']['tmp_name'][$name]);
                    if ($image->getWidth() > self::MAX_RESOLUTION) {
                        $image->resizeToWidth(self::MAX_RESOLUTION);
                    }
                    if ($image->getHeight() > self::MAX_RESOLUTION) {
                        $image->resizeToHeight(self::MAX_RESOLUTION);
                    }
                    $image->save($newname);
                    $this->image_name[] = $image_name;
                } else {
                    $this->error = '<span class="imgList">Размер файла привышает допустимый!</span>';
                    return false;
                }
            } else {
                $this->error = '<span class="imgList">Недопустимое расширение файла!</span>';
                return false;

            }
        }
        return true;
    }

    public
    static function deleteImage($image_name)
    {
        unlink(_ROOT_PATH_ . self::$uploaddir . $image_name);
        return true;
    }

}