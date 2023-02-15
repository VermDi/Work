<?php
/**
 * Created by PhpStorm.
 * User: E_dulentsov
 * Date: 15.06.2017
 * Time: 14:39
 */

namespace modules\menu\widgets;


use core\Html;
use modules\menu\models\Menu;

class Show
{
	public static function Menu($id, $template = "default.php")
	{
		if ($id > 0) {
			$model = new Menu();
			if ($menu = $model->getOne($id)) {
				if ($menu->id > 0) {
					$left_key = $menu->left_key;
					$right_key = $menu->right_key;
					$data = $model->clear()->where('left_key', '>', $left_key)
								  ->where('right_key', '<', $right_key)
								  ->where('level', '=', 1)
								  ->where('visible', '=', 1)
								  ->orderBy('position ASC, left_key ASC')->getAll();
					echo Html::instance()->render(__DIR__ . "/templates/" . $template, $data);
				}
			}
		}
	}

	public static function MenuByUrl($url, $template = "default.php")
	{
		if (!empty($url)) {
			$model = new Menu();

			if ($menu = $model->where('url', '=', $url)->getOne()) {
				if ($menu->level == 2) {
					$menu = $model->clear()
								  ->where('left_key', '<', $menu->left_key)
								  ->where('right_key', '>', $menu->right_key)
								  ->where('level', '=', '1')
								  ->getOne();
				}

				if ($menu->id > 0) {
					$left_key = $menu->left_key;
					$right_key = $menu->right_key;
					$data = $model->clear()->where('left_key', '>', $left_key)
								  ->where('right_key', '<', $right_key)
								  ->where('visible', '=', 1)
								  ->orderBy('position ASC, left_key ASC')
								  ->getAll();
					return Html::instance()->render(__DIR__ . "/templates/" . $template, $data);
				}
			}
			return true;
		}
		return false;
	}

	public static function NestedMenuById($id, $template = "default.php")
	{
		if ($id > 0) {
			$model = new Menu();
			if ($menu = $model->getOne($id)) {
				if ($menu->id > 0) {
					$left_key = $menu->left_key;
					$right_key = $menu->right_key;
					$data = $model->clear()
								  ->where('left_key', '>', $left_key)
								  ->where('right_key', '<', $right_key)
								  ->where('visible', '=', 1)
								  ->orderBy('position ASC, left_key ASC')
								  ->getAll();
					$data = self::prepareData($data);

					echo Html::instance()->render(__DIR__ . "/templates/" . $template, $data);
				}
			}
		}
	}

	private static function prepareData($data, $lk = false, $rk = false, $level = 1)
	{
		$newArrOfMenu = [];
		if (is_array($data)) {
			foreach ($data as $k => $v) {
				if ($lk == false and $rk == false and $v->level == $level) { //первый прогон

					$sub = self::prepareData($data, $v->left_key, $v->right_key, $level + 1);
					if (is_array($sub) and count($sub) > 0) {
						$v->subMenu = $sub;
					}
					$newArrOfMenu[] = $v;
					unset($data[$k]); //убираем использованое
				} else {
					if ($v->left_key > $lk and $v->right_key < $rk and $v->level == $level) {
						$sub = self::prepareData($data, $v->left_key, $v->right_key, $level + 1);
						if (is_array($sub) and count($sub) > 0) {
							$v->subMenu = $sub;
						}
						$newArrOfMenu[] = $v;
						unset($data[$k]); //убираем использованное
					}
				}
			}
		}
		return $newArrOfMenu;
	}

	public static function jsonMenu($id)
	{
		$data = [];
		if ($id > 0) {
			$model = new Menu();
			if ($menu = $model->getOne($id)) {
				if ($menu->id > 0) {
					$left_key = $menu->left_key;
					$right_key = $menu->right_key;
					$data = $model->clear()
								  ->where('left_key', '>', $left_key)
								  ->where('right_key', '<', $right_key)
								  ->where('visible', '=', 1)
								  ->orderBy('position ASC, left_key ASC')
								  ->getAll();
					$data = self::prepareData($data);
				}
			}
		}
		return $data;
	}
}