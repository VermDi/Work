<?php

namespace modules\pages\controllers;

use core\App;
use core\Controller;
use core\Db;
use core\Errors;
use core\FS;
use core\Html;
use core\Imgcache;
use core\Response;
use modules\historian\helpers\History;
use modules\pages\helpers\Nested;
use modules\pages\models\Page;
use modules\pages\services\Pages;

class Admin extends Controller
{
	private $nested;
	public  $path_to_image = _ROOT_PATH_ . "/public/images/pages/";

	function __construct()
	{
		if (App::$url['way'][0] !== 'pages') {
			Errors::e404('НЕТ ТАКОГО');
		}
		parent::__construct();
		$this->model = new \modules\pages\models\Page();
		$this->model->table = "pages";
		$this->nested = new \Nested();
		$this->nested->pdo = Db::getPdo();
		$this->nested->left = 'left_key';
		$this->nested->right = 'right_key';
		$this->nested->level = 'level';
		$this->nested->table = 'pages';
	}

	/**
	 * Выводит листинг страниц с помощь jstree
	 */
	public function actionIndex()
	{
		$conf = $this->settings();
		$domains = false;
		$currentWorkDomain = false;
		if ($conf['isMultiDomains'] !== false) {
			$domains = Pages::getDomains(); // берем перечень из бд, чтобы не показывать лишнего
		}
		if (isset($_GET['domain'])) {
			$currentWorkDomain = htmlspecialchars($_GET['domain']);
		}
		Html::instance()->content = $this->render("Pageslist.php", ['data' => Pages::GetForList($currentWorkDomain), 'domains' => $domains]);
		Html::instance()->renderTemplate("@admin")->show();
	}

	public function actionGetList()
	{
		$conf = $this->settings();
		$domains = false;
		$currentWorkDomain = false;
		if ($conf['isMultiDomains'] !== false) {
			$domains = Pages::getDomains(); // берем перечень из бд, чтобы не показывать лишнего
		}
		if (isset($_GET['domain'])) {
			$currentWorkDomain = htmlspecialchars($_GET['domain']);
		}

		return (Nested::printAjaxJsonTree(Pages::GetForList($currentWorkDomain), 0, false));
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
		$conf = $this->settings();
		$domains = false;
		if ($conf['isMultiDomains'] !== false) {
			$domains = $conf['domains']; //Берем доступный перечень доменов из настроек...
		}

		if ($pid == false) {
			Html::instance()->content = $this->render("Pageform.php", [$this->model->factory($id), $pid, $domains]);
		} else {
			Html::instance()->content = $this->render("Pageform.php", [$this->model->factory($id), $pid, $domains]);
		}
		if ($ajax) {
			echo Html::instance()->content;
		} else {
			Html::instance()->renderTemplate("@admin")->show();
		}
		die();
	}

	public function actionSaveFromFront($id)
	{

		if ($this->model->factory($id)->fill($_POST)->save()) {
			Response::ajaxSuccess('Сохранено успешно');
		} else {
			Response::ajaxError('ОШИБКА');
		}


	}

	/**
	 * Выводит историю изменений при наличии историка
	 *
	 * @param $id
	 *
	 * @throws \Exception
	 */
	public function actionGetHistory($id)
	{
		if (!class_exists('modules\historian\helpers\History')) {
			echo "NO HISTORY";
			return;
		}
		echo $this->render('history.php',
			History::getHistory('pages', $id));
		return;
	}

	/**
	 * Метод откатывает изменения
	 *
	 * @param $id
	 *
	 * @throws \Exception
	 */
	public function actionRallBack($id)
	{
		if (!class_exists('modules\historian\helpers\History')) {
			echo "NO HISTORY";
			return;
		}
		$obj = History::getById($id);
		if ($obj->mod_key != 'pages') {
			throw  new \Exception('Не верно передан модуль');
		}
		$val = unserialize($obj->value);
		if (!is_object($val)) {
			throw new \Exception('Получен не объект, вероятно есть поломка');
		}
		/* --- Так как в объекте есть первичный ключ, это будет АПДЕЙТ -- */
		$this->model->save($val);
		/* TODO добавить для АЯКСА методы */
		header("Location: " . $_SERVER['HTTP_REFERER']);

	}

	/**
	 * Сохранение изменений
	 */
	public function actionSave()
	{
		// НЕ ЗАЫБВАЕМ про модель там есть before
		//Случай вложенности, самый сложный вариант когда мы сохраняем дочерний элемент
		if (isset($_POST['pid'])) {
			$pid = intval($_POST['pid']);
			$this->model->clear()->getOne($pid);
			/*
			 * Двигаем границы....
			 */
			if ($this->model->id > 0) {
				$time = new Page();
				$time->select('MAX(position) as pos')
					 ->where('right_key', '<', $this->model->right_key)
					 ->where('left_key', '>', $this->model->left_key)
					 ->where('level', '=', $this->model->level + 1)->getOne();
				//сдвигаем всех...
				$sql = "UPDATE pages set left_key = left_key + 3, right_key=right_key+3 where left_key > " . $this->model->right_key;
				DB::getPdo()->Query($sql); //все подвинули за границей
				//Сдвигаем все внутри
				$sql = "UPDATE pages SET right_key = right_key + 3 WHERE right_key >= " . $this->model->right_key . " AND left_key < " . $this->model->right_key;
				DB::getPdo()->Query($sql); //все подвинули

				$_POST['level'] = $this->model->level + 1;
				$_POST['left_key'] = $this->model->right_key;
				$_POST['right_key'] = $this->model->right_key + 1;
				$this->model->right_key = $this->model->right_key + 2;
				$_POST['position'] = $time->pos + 1;
				$this->model->save();
			}
		}
		/*
		 * Когда мы правим запись
		 */
		if (!empty($_POST['id'])) {
			$id = intval($_POST['id']);
			$_POST['bg_img'] = $this->saveImage($id);
		} else {
			$id = false;
		}
		/*
		 * Далее стандартно все...
		 */
		$this->model->clear();
		if ($newid = $this->model->factory($id)->fill($_POST)->save()) {
			if (!$id and $newid > 0) {
				//$this->bg_img = $this->saveImage($newid);
			}
			echo json_encode(['data' => $this->model->getOne($this->model->insertId()), 'error' => 0]);
		} else {
			echo json_encode(["error" => 1, 'data' => 'что то не так']);
		}
	}

	/**
	 * Сохраняет картинку
	 * */
	public function saveImage($id)
	{
		$pathOnId = md5($id);
		if (isset($_FILES['bg_img']) and !empty($_FILES['bg_img']['name'])) {
			if (!file_exists($this->path_to_image . $pathOnId . "/")) {
				FS::instance()->createFolder($this->path_to_image . $pathOnId . "/");
			}
			if (file_exists($this->path_to_image . $pathOnId . "/bg.jpg")) {
				unlink($this->path_to_image . $pathOnId . "/bg.jpg");
			}
			$this->removeImage($id);
			$file = new \upload($_FILES['bg_img']);
			if ($file->uploaded) {

				$file->file_new_name_body = 'bg';
				$file->image_resize = true;
				$file->image_x = 1900;
				$file->image_convert = 'jpg';
				$file->image_ratio_y = true;
				$file->process($this->path_to_image . $pathOnId . "/");
				if ($file->processed) {
					$file->clean();
//                    $this->image = $this->id;
					return $this->path_to_image . $pathOnId . "/";

				} else {
					return false;
				}
			}
		}
	}

	/**
	 * Удаляет картинку новости
	 *
	 * @return bool
	 */
	public function removeImage($id)
	{
		$pathOnId = md5($id);
		if (file_exists($this->path_to_image . $pathOnId . "/bg.jpg")) {
			if (FS::instance()->removeFolder($this->path_to_image . $pathOnId . "/", 1)) {
				Imgcache::clearCache('module-pages' . $pathOnId);
				return true;
			} else {
				return false;
			}
		}
		return true;
	}

	public function getDeleteImg($id)
	{
		$this->removeImage($id);
		Response::back();
	}

	/**
	 * Удаление старницы
	 *
	 * @param $id
	 */
	public function actionDelete($id)
	{
		if ($this->nested->deleteNode($id)) {
			echo json_encode(['error' => 0, 'data' => 'Успешно']);
		} else {
			echo json_encode(['error' => 1, 'data' => 'Ошибка удаления ветки!']);
		}
		die();
	}

	/**
	 * Удаление элемента ветки со сдвигом
	 *
	 * @param $id
	 */
	public function actionDeleteElement($id)
	{
		if ($this->nested->delete($id)) {
			echo json_encode(['error' => 0, 'data' => 'Успешно']);
		} else {
			echo json_encode(['error' => 1, 'data' => 'Ршибка удаления элемента!']);
		}
		die();
	}

	/**
	 * Перенос по дереву
	 * Работает на пост данный по аяксу..
	 */
	public function actionMove()
	{
		// id: 2
		// old_parent: NaN
		// new_parent: NaN
		// old_position: 0
		// new_position: 1

		$error = ['result' => 1, 'error' => 0];
		$id = intval($_POST['id']);
		$pid = intval($_POST['new_parent']);
		$old = intval($_POST['old_position']);
		$new = intval($_POST['new_position']);
		// Получаем родителя, или новое место, у нас два случая, есть пид - значит перенос зависимости, нет пида, перенос на первый уровень

		$old_parent = intval($_POST['old_parent']);
		$new_parent = intval($_POST['new_parent']);

		if ($old_parent == $new_parent) {
			$current = $this->model->clear()->getOne($id);
			$delta = $old - $new;
			if ($delta < 0) {
				$delta = $delta * (-1);
			}

			// Аж стыдно надо при свободном времени исправить код
			$parent = $this->model->clear()
								  ->where('left_key', '<', $current->left_key)
								  ->where('right_key', ">", $current->right_key)->getOne();
			if ($old > $new) {
				$new_position_to_current = $current->position - $delta;
			} else {
				$new_position_to_current = $current->position + $delta;
			}
			$this->model->clear()->factory(false)->fill($current);
			$real_old_position = $this->model->position;
			$this->model->position = $new_position_to_current;
			$this->model->save();
			if (!$parent) {
				Db::getPdo()->query("UPDATE " . $this->model->table . " set `position` = `position` + 1 where `position` >= " . $new_position_to_current . " and " . 'level = 0 and id != ' . $id);
			} else {
				if ($real_old_position < $new_position_to_current) {
					Db::getPdo()->query("UPDATE " . $this->model->table . " set 
				`position` = `position` - 1 
				 where `position` >= " . $real_old_position .
						"and `position` <= " . $new_position_to_current .
						" and " . 'left_key >' . $parent->left_key .
						' and level = ' . $current->level .
						' and  right_key < ' . $parent->right_key .
						" and id != " . $id);
				} else {
					Db::getPdo()->query("UPDATE " . $this->model->table . " set 
				`position` = `position` + 1 
				 where `position` >= " . $new_position_to_current .
						"and `position` <= " . $real_old_position .
						" and " . 'left_key >' . $parent->left_key .
						' and level = ' . $current->level .
						' and  right_key < ' . $parent->right_key .
						" and id != " . $id);
				}
			}

		} else {
			if (empty($pid)) {
				if (!$this->nested->makeNodeRoot($id)) {
					$error = ['result' => 0, 'error' => 'Не удалось переместить ветку в корень'];
				}
			} else {
				if (!$this->nested->moveNode($id, $pid)) {
					$error = ['result' => 0, 'error' => 'Не удалось переместить веточку.'];
				}
			}
		}
		die(json_encode($error));
	}

	public function actionRebuild()
	{
		$m = Page::instance()->select('id')->orderBy('left_key ASC, position ASC')->getAll();

		$i = 0;
		foreach ($m as $item) {
			$i++;
			Page::instance()->clear()->save(['id' => $item->id, 'position' => $i]);
		}
		Response::back();
	}
}