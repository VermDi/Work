<?php namespace basemodules\base\controllers;

use core\App;
use core\Controller;
use core\Html;

/**
 * Created by PhpStorm.
 * User: E_dulentsov
 * Date: 11.11.2017
 * Time: 13:45
 */
abstract class Simpleadmin extends Controller
{

	public $model;
	public $pk_save;

	/**
	 * Выводит список телефонов
	 */
	public function actionIndex()
	{
		Html::instance()
			->setJs("/assets/vendors/datatables/js/jquery.dataTables.min.js");
		Html::instance()
			->setCss("/assets/vendors/datatables/css/jquery.dataTables.min.css");
		$modeName                 = explode("\\", get_class($this));
		Html::instance()->content = $this->render("/".end($modeName)."List.php");
		Html::instance()
			->renderTemplate("@admin")
			->show();
	}

	/**
	 * Когда надо что то сделать до отдачи списка
	 */
	public function beforeGetList()
	{
		return true;
	}

	/**
	 * Когда надо что то сделать до отдачи списка
	 */
	public function afterGetList()
	{
		return true;
	}

	/**
	 * Возвращает json листинг моделей
	 */
	public function actionGetList()
	{
		if (!$this->beforeGetList()) {
			throw new \Exception('УХ ОШИБКА, до получения листа!');
		}
		if (!$arr = $this->model->getAll()) {
			echo json_encode([]);
			die();
		}
		if (!$this->afterGetList()) {
			throw new \Exception('УХ ОШИБКА, после получения листа!');
		}
		/*
		 * Подготовим управление... И все :) Заработало
		 */
		foreach ($arr as $k => $v) {
			$arr[$k]->control = '<a href=\'/'.App::$module.'/'.App::$controller.'/form/'.$v->id.'\' class=\'btn btn-xs btn-warning\'><i class=\'fa fa-edit\'></i></a>';
			$arr[$k]->control .= '<a href=\'/'.App::$module.'/'.App::$controller.'/delete/'.$v->id.'\' class=\'btn btn-xs btn-danger\'><i class=\'fa fa-remove\'></i></a>';
		}
		if (!$arr) {
			echo json_encode([]);
		} else {
			echo json_encode(['data' => $this->prepareCollection($arr)]);
		}
		die();
	}

	/**
	 * Подготоавливаем данные
	 *
	 * @param $arr
	 *
	 * @return mixed
	 */
	public function prepareCollection($arr)
	{
		return $arr;
	}

	/**
	 * Форма для создания - редактирования телефона
	 *
	 * @param bool $id
	 * @param bool $ajax
	 *
	 * @throws \Exception
	 */
	public function actionForm($id = false, $ajax = false)
	{
		if ($id!=false and !is_numeric($id)) {
			throw new \Exception('Ошибка');
		}
		$data     = $this->model->factory($id);
		$modeName = explode("\\", get_class($this));
		$content  = $this->render(
			"/".end($modeName)."Form.php", [
			'data'   => $data,
			'config' => $this->config
		]
		);
		if (!$ajax) {
			Html::instance()->content = $content;
			Html::instance()
				->renderTemplate("@admin")
				->show();
		} else {
			echo $content;
		}
	}

	/**
	 * Когда нужно что то сделать с данными, до сохранения данных
	 * @return bool
	 */
	public function beforeSave()
	{
		return true;
	}

	/**
	 * Когда нужно что то сделать после сохранения данных
	 * @return bool
	 */
	public function afterSave()
	{
		return true;
	}

	/**
	 * Сохраяем данные
	 */
	public function actionSave()
	{
		$this->model->factory()
					->fill($_POST);
		if ($this->beforeSave()) {
			if (!$this->pk_save = $this->model->save()) {
				throw new \Exception('ОШИБКА!');
			}
		}
		if ($this->afterSave()) {
			header("Location: /".App::$module."/".App::$controller."/ ");
		}
		die();
	}

	/**
	 * Когда нужно что то сделать с данными, до сохранения данных
	 * @return bool
	 */
	public function beforeDelete()
	{
		return true;
	}

	/**
	 * Удаление
	 *
	 * @param $id
	 *
	 * @throws \Exception
	 */
	public function delete($id)
	{
		if ($this->beforeDelete()) {
			if ($this->model->delete($id)) {
				header("Location: ".$_SERVER['HTTP_REFERER']);
			} else {
				throw new \Exception('ОШИБКА!');
			}
		} else {
			throw new \Exception('ОШИБКА ДО УДАЛЕНИЯ!');
		}
	}
}