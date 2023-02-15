<?php

namespace modules\menu\controllers;

use core\Controller;
use core\Db;
use core\Html;
use core\Parameters;
use core\Response;
use modules\menu\models\Menu;
use modules\menu\services\MenuActions;
use modules\menu\widgets\Show;

class Admin extends Controller
{
	function __construct()
	{
		parent::__construct();
		$this->model = new \modules\menu\models\Menu();
		$this->nested = new \Nested();
		$this->nested->pdo = DB::getPdo();
		$this->nested->left = 'left_key';
		$this->nested->right = 'right_key';
		$this->nested->level = 'level';
		$this->nested->table = 'menu';
	}

	public function actionIndex()
	{
		Html::instance()->content = $this->render("Menulist.php", $this->model->GetForList());
		Html::instance()->renderTemplate("@admin")->show();

	}

	/**
	 * Рисует форму для аякс без дизайна, а без аякса - дизайн админки
	 * Дата передается на основании id
	 *
	 * @param bool $id
	 * @param bool $ajax
	 */
	public function actionForm($id = false, $ajax = false, $pid = false)
	{
		if ($id == 0) {
			$id = false;
		} //на всякий случай
		if ($pid == false) {
			Html::instance()->content = $this->render("Menuform.php", [$this->model->factory($id), $pid]);
		} else {
			Html::instance()->content = $this->render("Menuform.php", [$this->model->factory($id), $pid]);
		}
		if ($ajax) {
			echo Html::instance()->content;
		} else {
			Html::instance()->renderTemplate("@admin")->show();
		}
		die();
	}

	public function actionParameters()
	{
		html()->setJs('/assets/vendors/e-mindhelpers/EM.js');
		html()->content = $this->render('extParameters.php', Parameters::get('module-menu'));
		html()->renderTemplate('@admin')->show();
	}

	public function postSaveParameters()
	{
		Parameters::set(json_decode($_POST['data']), 'module-menu');
		Response::ajaxSuccess('ok');
	}

	public function actionSave()
	{
		echo MenuActions::saveMenu();
	}

	public function actionDelete($id)
	{
		/*
		 * Удаление просто красота, но смотреть модельку!!!! Там есть before и это пока без пересчета!!!!
		 */
		$this->model->factory($id);
		if ($this->model->delete()) {
			echo json_encode(['error' => 0, 'data' => 'ВСЕ ГУД!']);
		} else {
			echo json_encode(['error' => 1, 'data' => 'ОПА!']);
		}
		die();
	}

	public function actionChangePosition()
	{
		/*
		  * Получаем родителя, или новое место, у нас два случая, есть пид - значит перенос зависимости, нет пида, перенос на первый уровень
		  */
		$message = false;
		$id = intval($_POST['id']);

		$old = intval($_POST['old_position']);
		$newPosition = intval($_POST['position']);
		$real_position = $newPosition - $old;
		if ($real_position < 0) {
			$real_position = $real_position * (-1);
		}
		if (is_numeric($_POST['parent_id'])) {
			$pid = intval($_POST['parent_id']);
			$parent = $this->model->clear()->getOne($pid);
		} else {
			$parent = false;
		}
		$current = $this->model->clear()->factory($id);
		if ($current->level - 1 != $parent->level) {
			/*
			 * Происходит смещение ветки в более высокий уровень
			 */
			Db::getPdo()->query("UPDATE " . $this->model->table . " set `right_key` = `right_key` + 2 where id = ".$parent->id);
			$current->level = $parent->level + 1;
			$current->left_key = $parent->right_key;
			$current->right_key = $parent->right_key+1;
			$current->save();
			Response::ajaxSuccess('MOVED');
			die();

		}

		if (is_object($current) and is_object($parent) and $current->left_key > $parent->left_key and $current->right_key < $parent->right_key) {


			/*
			 * значит это смещение по порядку
			 */
			/*
			 * 1 Получим запись.
			 *
			 * */


			if ($old > $newPosition) {
				$old = $current->position;
				$current->position = $current->position - $real_position;
				Db::getPdo()->query("UPDATE " . $this->model->table . " set `position` = `position` + 1
                 where `position` >= " . ($current->position) . " and `position` <= " . $old . " and " . 'left_key >' . $parent->left_key . ' and right_key < ' . $parent->right_key . " 
                 and level ='" . $current->level . "' and id != " . $id);

			} else {
				$old = $current->position;
				$current->position = $current->position + $real_position;
				Db::getPdo()->query("UPDATE " . $this->model->table . " set `position` = `position` - 1 
                where `position` <= " . ($current->position) . " and `position` >= " . $old . " and " . 'left_key > ' . $parent->left_key . ' and right_key < ' . $parent->right_key . "
                 and level = '" . $current->level . "' and id != " . $id);

			}

			$current->save();
			//все нашли, меняем их местами

			Response::ajaxSuccess('MOVED');

		} else {

			/*
			 * это переезд в другую ветку
			 */
			if (empty($pid)) {
				if (!$this->nested->makeNodeRoot($id)) {
					$message = 'Не удалось переместить ветку в корень';

				}
			} else {
				if (!$this->nested->moveNode($id, $pid)) {
					$message = 'Не удалось переместить веточку.';
				}
			}
			$this->model->clear()->factory($id);
			$this->model->position = $newPosition;
			$this->model->save(); //обновили основную запись, смещаем остальне
			if ($message) {
				Response::ajaxError($message);
			} else {
				Response::ajaxSuccess($message);
			}
		}

	}

	/**
	 * Перестроить все дерево по позициям
	 */
	public function actionRebuild()
	{
		$m = Menu::instance()->select('id')->orderBy('left_key ASC, position ASC')->getAll();

		$i = 0;
		foreach ($m as $item) {
			$i++;
			Menu::instance()->clear()->save(['id' => $item->id, 'position' => $i]);
		}
		Response::back();


	}

}