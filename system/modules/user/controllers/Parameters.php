<?php
/**
 * Create by e-Mind Studio
 * User: noutbuk
 * Date: 03.07.2018
 * Time: 20:43
 */

namespace modules\user\controllers;


use core\Controller;
use core\Html;
use modules\user\models\USER;

class Parameters extends Controller
{

	public function actionIndex()
	{
		$params = \core\Parameters::instance()->GetParametersAsArray('user_avatar');

		if (empty($params)) {
			$params = include($this->getModulePath() . "/set.php");
			$params = $params['params'];
			\core\Parameters::instance()->SetParameters($params, 'user_avatar');
		}
		Html::instance()->content = $this->render("parameters/index.php", ['params' => $params]);
		if(USER::current()->is('admin')){
			Html::instance()->renderTemplate("@admin")->show();
		}else{
			Html::instance()->renderTemplate("index")->show();
		}
		exit;
	}

	public function actionSave()
	{
		$params = \core\Parameters::instance()->GetParametersAsArray('user_avatar');
		if (!empty($_POST)) {
			foreach ($_POST as $key => $item) {
				$params[$key]['value'] = $item;
			}
			if (\core\Parameters::instance()->SetParameters($params, 'user_avatar')) {
				header('Location: /user');
			} else {
				$_SESSION['error'] = 'Не удалось сохранить настройки';
				header('Location: /user/parameters');
			}
		} else {
			$_SESSION['error'] = 'Нечего сохранять.';
			header('Location: /user/parameters');
		}
		exit;

	}

	public function actionGet()
	{
		$params = \core\Parameters::instance()->GetParametersAsArray('user_avatar');
		//echo"<pre>";print_r($params);echo"</pre>";
		echo json_encode($params['size_avatar']);
	}

}