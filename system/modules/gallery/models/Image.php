<?php

namespace modules\gallery\models;

use core\Model;
use modules\gallery\helpers\ImageUpload;

/**
 * Class Index
 * @property string id -
 * @property string module_name -
 * @property string key_id -
 * @property string temp_id -
 * @property string image_name -
 * @property string title -
 * @property string position -
 * @property string is_main -
 */
class Image extends Model
{
    public $table = 'gallery_images';

    const IS_MAIN  = 1;
    const NOT_MAIN = 2;

    public function factory($id = false)
    {
        if ($id == false or !$this->getOne($id)) {
            $this->id          = "";
            $this->module_name = "";
            $this->key_id      = "";
            $this->temp_id     = "";
            $this->image_name  = "";
            $this->title       = "";
            $this->position    = "";
            $this->is_main     = "";

        }
        return $this;
    }

    public function getMaxPosition($params)
    {
        $this->max('position', 'maxPosition')->where($params)->getOne();
        return $this->maxPosition;
    }

    public function deleteAndUnlink($id)
    {
        $imageOne  = $this->clear()->getOne($id);
        $imageNext = $this->clear()->where(['key_id' => $imageOne->key_id, 'temp_id' => $imageOne->temp_id, 'module_name' => $imageOne->module_name])->where('position', '>', $imageOne->position)->getAll();
        if (!empty($imageNext)) {
            foreach ($imageNext as $item) {
                $this->clear()->factory($item->id);
                $this->position--;
                $this->save();
            }
        }

        $this->clear()->delete($_POST['id']);

        $image                    = new ImageUpload($imageOne->module_name . DIRECTORY_SEPARATOR . $imageOne->key_id);
        ImageUpload::deleteImage($imageOne->image_name);

        return $imageOne;

    }

    public function renameTempDirAndUpdate($module_name, $tempDirName, $id)
    {
        $this->where(['temp_id' => $tempDirName])->update(['temp_id' => 0, 'key_id' => $id]);
        $path1=realpath(_ROOT_PATH_ . "/" . ImageUpload::$uploaddir . $module_name . "/" . $tempDirName);
        $path2=realpath(_ROOT_PATH_ . "/" . ImageUpload::$uploaddir . $module_name . "/" . $id);
        if (file_exists($path1) and !empty($path2) ) {
            rename($path1, $path2);
        }

    }
}