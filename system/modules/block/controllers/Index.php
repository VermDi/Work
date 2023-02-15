<?php
/**
 * Created by PhpStorm.
 * User: Pash
 * Date: 26.09.2015
 * Time: 16:10
 */

namespace modules\block\controllers;

use core\Html;
use core\Request;
use \modules\block\models\Block;
use \modules\user\models\Role;
use core\Controller;

/**
 * @property-read Block $model
 * Class Index
 * @package modules\block\controllers
 */
class Index extends Controller
{
    public $model;

    /**
     * @param Block $model
     */
    private function setModel(Block $model)
    {
        $this->model = $model;
    }

    public function init()
    {
        $this->setModel(new Block());
        Html::instance()->setJs("/assets/vendors/datatables/js/jquery.dataTables.min.js");
        Html::instance()->setJs("/assets/vendors/datatables/js/dataTables.treeGrid.js");
        Html::instance()->setCss("/assets/vendors/datatables/css/jquery.dataTables.min.css");
        Html::instance()->setJs('/assets/modules/block/src/main.js');
        Html::instance()->setJs('/assets/vendors/bootstrap-select/js/bootstrap-select.js');
        Html::instance()->setJs('/assets/vendors/bootstrap-select/js/i18n/defaults-ru_RU.js');
        Html::instance()->setCss('/assets/vendors/bootstrap-select/css/bootstrap-select.css');
        Html::instance()->setJs('/assets/vendors/ckeditor/ckeditor.js');
        Html::instance()->setTemplate('@admin');
    }

    public function actionIndex($id = 0)
    {
        Html::instance()->content = $this->render('index.php');
        Html::instance()->renderTemplate()->show();
    }

    protected function recurse($arr, $id)
    {
        $i = 0;
        $newArr = [];
        foreach ($arr as $k => $v) {
            if ($v->pid == $id) {
                $v = $this->prepareLine($v);
                $newArr[$i] = $v;
                if (count($r = $this->recurse($arr, $v->id)) > 0) {
                    $newArr[$i]->children = $r;
                }
                $i++;
            }
        }
        return $newArr;
    }

    public function actionSave($id = false, $pid = 0)
    {

        $this->setModel(new Block());
        $item = $this->model->factory($id)->fill($this->app->request->asArray());
        $item->rights = is_array($this->app->request->rights) ? implode(',', $this->app->request->rights) : "NULL";
        if (empty($item->pid)) {
            $item->pid = $pid;
        }
        $item->is_editor_enabled = isset($this->app->request->is_editor_enabled) ? '1' : 'NULL';
        if ($errors = $item->validate()) {
            $result['errors'] = $errors;
        } else {

            $id = $item->save();
            $data = (object)$item->factory($id)->toArray();

            $result['lineData']['id'] = $id;
            $result['lineData']['data'] = $this->prepareLine($data);

            $result['errors'] = false;
        }
        header('Content-type: application/json');
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        exit;
    }


    public function actionTable()
    {
        $this->setModel(new Block());
        return ['data' => $this->recurse($this->model->getAll(), 0)];
        //[0] => stdClass Object ( [id] => 1 [name] => 222 [title] => 222 [content] => 222 [pid] => 0 [rights] => [type] => 0 [is_editor_enabled] => ) [1] => stdClass Object ( [id] => 2 [name] => 2223 [title] => 2223 [content] => 2223 [pid] => 1 [rights] => [type] => 0 [is_editor_enabled] => ) [2] => stdClass Object ( [id] => 3 [name] => 444 [title] => 444 [content] => 4444 [pid] => 0 [rights] => [type] => 0 [is_editor_enabled] => ) [3] => stdClass Object ( [id] => 4 [name] => 22266 [title] => 22266 [content] => 222666 [pid] => 1 [rights] => [type] => 0 [is_editor_enabled] => ) )
        echo $this->render(
            'blocks.php',
            [
                'blocks' => $this->recurse($this->model->getAll(), 0)
            ]);
        exit;
    }

    public function actionAdd($pid = 0)
    {
        $this->setModel(new Block());

        $role = new Role();
        Html::instance()->content = $this->render('add.php', ['pid' => $pid, 'roles' => $role->getAll()]);
        if (!Request::instance()->isAjax()) {
            Html::instance()->renderTemplate()->show();
        } else {
            echo Html::instance()->content;
            die();
        }
    }


    public function actionCopy($pid = 0)
    {
        $this->setModel(new Block());
        $role = new Role();
        $this->model->getOne($pid);
        $this->model->id = "";
        Html::instance()->content = $this->render('edit.php', ['item' => $this->model, 'pid' => $pid, 'roles' => $role->getAll()]);
        if (!Request::instance()->isAjax()) {
            Html::instance()->renderTemplate()->show();
        } else {
            echo Html::instance()->content;
            die();
        }
    }

    public function actionEdit($id)
    {
        $this->setModel(new Block());
        $role = new Role();
        $this->model->getOne($id);
        Html::instance()->content = $this->render('edit.php', ['item' => $this->model, 'roles' => $role->getAll()]);
        if (!Request::instance()->isAjax()) {
            Html::instance()->renderTemplate()->show();
        } else {
            echo Html::instance()->content;
            die();
        }
    }

    public function actionDelete($id)
    {
        $id = intval($id);
        $this->setModel(new Block());
        $result['id'] = $id;
        $item = $this->model->getOne($id);
        if ($this->app->request->method() == 'POST') {
            try {
                $this->model->delete(['id' => $id]);
                $this->model->clear()->where('pid', '=', $id)->delete();
                $result['errors'] = false;
            } catch (\Exception $e) {
                $result['errors'] = $e->getMessage();
            }
            header('Content-type: application/json');
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
            exit;
        } else {
            Html::instance()->content = $this->render('delete.php', ['item' => $item]);
            if (!Request::instance()->isAjax()) {
                Html::instance()->renderTemplate()->show();
            } else {
                echo Html::instance()->content;
                die();
            }
        }
    }

    protected function prepareLine($line)
    {

        $line->name = "{#" . $line->name . "#}";
        $line->control = "<div class=\"btn-group\">
                            <a class=\"btn btn-xs btn-success\" href=\"/block/add/" . $line->id . "\" onclick=\"loadWindow(this); return false;\"><i
                                        class=\"glyphicon glyphicon-plus\"></i> добавить</a>
                            <a class=\"btn btn-xs btn-success\" href=\"/block/copy/" . $line->id . "\" onclick=\"loadWindow(this); return false;\"><i
                                        class=\"glyphicon glyphicon-copy\"></i> Копировать</a>
                            <a class=\"btn btn-xs btn-warning\" href=\"/block/edit/" . $line->id . "\" onclick=\"loadWindow(this); return false;\"><i
                                        class=\"glyphicon glyphicon-edit\"></i> править</a>
                            <a class=\"btn btn-xs btn-danger\" href=\"/block/delete/" . $line->id . "\" onclick=\"loadWindow(this); return false;\"><i
                                        class=\"glyphicon glyphicon-trash\"></i> удалить</a>
                        </div>";
        return $line;
    }
}