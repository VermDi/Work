<?php

namespace modules\news\controllers;

use core\Controller;
use core\Html;
use modules\news\models\Article;
use modules\news\models\Categories;
use modules\news\repositories\Articles;
use modules\news\services\ControlPrepare;

class Admin extends Controller
{
    /**
     * Отрисовываем странцу
     *
     */
    public function actionIndex()
    {
        Html::instance()->setCss("/assets/vendors/toastr/dist/jquery.toast.min.css");
        Html::instance()->setJs("/assets/vendors/toastr/dist/jquery.toast.min.js");
        Html::instance()->setJs("/assets/vendors/datatables/js/datatables.min.js");
        Html::instance()->setCss("/assets/vendors/datatables/css/datatables.min.css");
        Html::instance()->setJs("/assets/vendors/ckeditor/ckeditor.js");
        Html::instance()->setJs("/assets/vendors/ckeditor/adapters/jquery.js");
        Html::instance()->content = $this->render("/admin/List.php", ['categories' => \modules\news\repositories\Categories::getActiveCategories()]);
        Html::instance()->renderTemplate("@admin")->show();

    }

    /**
     * Сохранение категории
     */

    public function postSaveCategory()
    {
        $model = Categories::instance()->factory();
        if ($model->fill($_POST)->save()) {
            die(json_encode(['success' => '1', 'data' => $model->getOne($model->insertId()), 'type' => 'cat']));
        }
    }

    /**
     * Сохранение статьи
     */
    public function postSaveArticle()
    {
        $model = Article::instance()->factory();
        if ($model->fill($_POST)->save()) {
            /*
             * Ситуация когда была вставка и картинка еще не сохранена
             * в идеале надо будет переатащить это все же в afterInsert но там какая то уйня щас идет
             */
            if ($model->insertId() != null) {
                die(json_encode(['success' => '1', 'data' => $model->getOne($model->insertId()), 'type' => "article"]));
            } else {
                die(json_encode(['success' => '1', 'data' => $model, 'type' => "article"]));
            }


        }
    }

    /**
     * Получаем список статей в админку. А значит все...
     * @param bool $category
     */
    public function actionGetList($category = false)
    {

        if ($category == false) {
            die(json_encode(['data' => ControlPrepare::setControl(Articles::getAllArticlesInCategory(0))]));
        } else {
            die(json_encode(['data' => ControlPrepare::setControl(Articles::getAllArticlesInCategory($category))]));
        }
    }

    /**
     * Форма для создания статьи
     * @param bool $id
     * @param int $cat
     * @throws \Exception
     */
    public function getArticleForm($id = false, $cat = 0)
    {
        $obj = Article::instance()->factory($id);
        $obj->categories_id = $cat;
        $data['body'] = $this->render("/admin/Articleform.php", $obj);
        die(json_encode($data));

    }

    /**
     * Удаление статьи
     * @param $id
     */
    public function getDeleteArticle($id)
    {
        $art = Article::instance()->factory($id);
        $art->removeImage();
        if (Article::instance()->delete($id)) {
            die(json_encode(['success' => '1']));
        }

    }

    /**
     * Удаление категории
     * @param $id
     */
    public function getDeleteCategory($id)
    {
        if (Categories::instance()->delete($id)) {
            die(json_encode(['success' => '1']));
        };
    }

    /**
     * Удаление картинки статьи
     * @param $id
     */
    public function getDeleteImage($id)
    {
        $art = Article::instance()->factory($id);
        if ($art->removeImage()) {
            $art->image = null;
            $art->save();
            die(json_encode(['success' => '1']));
        } else {
            die(json_encode(['error' => '1']));
        }

    }

    /**
     * Форма для категории
     * @param bool $id
     * @throws \Exception
     */
    public function getCategoryForm($id = false)
    {
        $data['body'] = $this->render("/admin/CatForm.php", Categories::instance()->factory($id));
        die(json_encode($data));
    }

}