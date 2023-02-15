<?php

namespace modules\menu\services;

use core\Db;
use modules\menu\models\Menu;

class MenuActions
{

	public static function saveMenu()
	{

		$f_model = Menu::instance();
		// НЕ ЗАЫБВАЕМ про модель там есть before
		//Случай вложенности, самый сложный вариант когда мы сохраняем дочерний элемент
		$position = 1;
		if (isset($_POST['pid'])) {
			$pid = intval($_POST['pid']);
			$f_model->clear()->getOne($pid);
			/*
			 * Двигаем границы....
			 */
			if ($f_model->id > 0) {
				$model = new Menu();
				$left_key = $f_model->left_key;
				$right_key = $f_model->right_key;
				$record = $model->clear()->select('max(position) as p')->where('left_key', '>', $left_key)->where('right_key', '<', $right_key)->getOne();
				$position = $record->p + 1;
				//сдвигаем всех...
				$sql = "UPDATE menu set left_key = left_key + 3, right_key=right_key+3 where left_key > " . $f_model->right_key;
				DB::getPdo()->Query($sql); //все подвинули за границей

				//Сдвигаем все внутри
				$sql = "UPDATE menu SET right_key = right_key + 3 WHERE right_key >= " . $f_model->right_key . " AND left_key < " . $f_model->right_key;
				DB::getPdo()->Query($sql); //все подвинули
				$_POST['level'] = $f_model->level + 1;
				$f_model->right_key = $f_model->right_key + 2;

				$_POST['left_key'] = $f_model->right_key - 2;
				$_POST['right_key'] = $f_model->right_key - 1;
				$f_model->save();
			}
		}
		/*
		 * Когда мы правим запись
		 */
		if (!empty($_POST['id'])) {
			$id = intval($_POST['id']);
		} else {
			$id = false;
		}
		/*
		 * Далее стандартно все...
		 */
		$f_model->clear();
		$f_model->factory($id)->fill($_POST);


		if (empty($_POST['is_nofollow'])) {
			$f_model->is_nofollow = 0;
		}
		if (empty($_POST['is_noindex'])) {
			$f_model->is_noindex = 0;
		}
		/*
		 * Если это обновление то позиция не меняется!
		 */
		if (!$id) {
			$f_model->position = $position;
		}
		$f_model->extData = extParametes::prepareExt($_POST);
		if ($f_model->save()) {
			return json_encode(['data' => $f_model->getOne($f_model->insertId()), 'error' => 0]);
		} else {
			return json_encode(["error" => 1, 'data' => 'что то не так']);
		}
	}


	public static function addInAdminMenu($url, $linkName)
	{
		$_POST['pid'] = 1;
		$_POST['url'] = $url;
		$_POST['name'] = $linkName;
		$_POST['visible'] = 1;
		self::saveMenu();

	}

}