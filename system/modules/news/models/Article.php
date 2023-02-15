<?php

namespace modules\news\models;

use core\FS;
use core\Imgcache;
use core\Model;

/**
 * Class Article
 * @property string id -
 * @property string categories_id -
 * @property string name -
 * @property string alias -
 * @property string data_create -
 * @property string title -
 * @property string data_end -
 * @property string full_article -
 * @property string short_article -
 * @property string deleted -
 * @property string meta_desc -
 * @property string meta_keywords -
 * @property string visible -
 */
class Article extends Model
{
    public $table = 'news_article';
    public $path_to_image = _ROOT_PATH_ . "/public/images/news/";

    /**
     * Класическая фабрика
     * @param bool $id
     * @return $this
     */
    public function factory($id = false)
    {
        if ($id == false or !$this->getOne($id)) {
            $this->id = null;
            $this->categories_id = 0;
            $this->name = "";
            $this->alias = "";
            $this->data_create = date("Y-m-d H:i:s", time());
            $this->title = "";
            $this->data_end = null;
            $this->full_article = "";
            $this->short_article = "";
            $this->deleted = 0;
            $this->meta_desc = "";
            $this->meta_keywords = "";
            $this->visible = 0;

        }
        return $this;
    }

    public function afterInsert()
    {
        $this->id = $this->insertId();
        $this->saveImage();
    }

    /**
     * Связь к категории
     */
    public function category()
    {
        return $this->belongsTo(Categories::instance(), 'id');
    }

    /**
     * Удаляет картинку новости
     * @return bool
     */
    public function removeImage()
    {
        if (file_exists($this->path_to_image . $this->id . "/" . $this->id . ".jpg")) {
            if (FS::instance()->removeFolder($this->path_to_image . $this->id . "/", 1)) {
                Imgcache::clearCache('newsArticle' . $this->id);
                return true;
            } else {
                return false;
            }
        }
        return true;
    }

    /**
     *
     * Показывает картинку
     */
    public function showImg($x = 200, $y = 200, $refresh = false)
    {
        if ($this->isImage()) {
            echo "<img src='" . Imgcache::getImg('newsArticle' . $this->id, $this->path_to_image . $this->id . "/" . $this->id . ".jpg", $x, $y) . (($refresh) ? "?" . time() : "") . "'>";
        } else {
            echo "BAD";
        }
    }

    /**
     * А картинка у статьи есть ?
     * @return bool
     */
    public function isImage()
    {
        if (file_exists($this->path_to_image . $this->id . "/" . $this->id . ".jpg")) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Перед сохранением надо сделать проверки
     * @return $this|void
     */
    public function beforeSave()
    {
        /*
        * Сделаем ссылку
        */
        if (empty($this->alias) and !empty($this->name)) {
            $this->alias = \URLify::filter($this->name);
        } else {
            $this->alias = \URLify::filter($this->alias);
        }
        /*
         * Уберем начальный слэш
         */
        if (substr($this->alias, 0, 1) == "/") {
            $this->alias = substr($this->alias, 1);
        }
        /*
         * Уберем конечный слэш
         *
         */
        if (substr($this->alias, 0, -1) == "/") {
            $this->alias = substr($this->alias, 0, -1);
        }
        if (empty($this->data_end)) {
            $this->data_end = null;
        }
        if (empty($this->visible)) {
            $this->visible = 0;
        }
        if (!empty($this->id)) {
            $this->saveImage();
        }
        /*
         * Есть картинка.. надо что то делать
         */

    }

    /**
     * Сохраянем картинку по правилу, /папка модели/id записи/id.jpg
     * @return bool|string
     */
    public function saveImage()
    {
        if (isset($_FILES['image']) and !empty($_FILES['image']['name'])) {
            if (!file_exists($this->path_to_image . $this->id . "/")) {
                FS::instance()->createFolder($this->path_to_image . $this->id . "/");
            }
            if (file_exists($this->path_to_image . $this->id . "/" . $this->id . ".jpg")) {
                unlink($this->path_to_image . $this->id . "/" . $this->id . ".jpg");
            }
            $this->removeImage();
            $file = new \upload($_FILES['image']);
            if ($file->uploaded) {

                $file->file_new_name_body = !empty($this->id) ? $this->id : rand(1, 9999);
                $file->image_resize = true;
                $file->image_x = 900;
                $file->image_convert = 'jpg';
                $file->image_ratio_y = true;
                $file->process($this->path_to_image . $this->id . "/");
                if ($file->processed) {
                    $file->clean();
//                    $this->image = $this->id;
                    return $this->path_to_image . $this->id . "/";

                } else {
                    return FALSE;
                }
            }
        }
    }
}