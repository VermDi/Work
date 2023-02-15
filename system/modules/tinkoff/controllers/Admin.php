<?php

namespace modules\tinkoff\controllers;

use core\Controller;
use core\Html;
use modules\tinkoff\models\TinkoffSettingsButton;
use modules\user_diet_extension\models\User_diet_extension;
use function mysql_xdevapi\expression;

class Admin extends Controller
{
	/* @var $html Html */
	private $html;

	static function instance()
	{
		return new Admin();
	}

	function __construct()
	{
		$this->html = Html::instance();
		$this->html->setCss('/assets/system/news/css/admin_styles.css');
		$this->html->setCss('/assets/modules/tinkoff/css/tinkoff_style.css');
		$this->html->setJs('/assets/modules/tinkoff/js/tinkoff.js');
		$this->html->setJs('/assets/vendors/datatables/js/jquery.dataTables.min.js');
		$this->html->setJs('/assets/vendors/bootstrap-datepicker/js/bootstrap-datepicker.js');
		$this->html->setJs('/assets/vendors/bootstrap-datepicker/js/bootstrap-datepicker.ru.min.js');
		$this->html->setJs('/assets/system/news/js/news_datatable.js');
		$this->html->setJs('https://forma.tinkoff.ru/static/onlineScript.js');
		parent::__construct($this->model);
	}

	public function actionIndex()
	{
        $this->html->title = 'Инструкция для вывода кнопки на проекте';
        $this->html->content = $this->render('admin/manual.php');
        $this->showTemplate();
	}

	public function actionSettingsButton()
	{
		$data = $_POST;

		if (empty($data['buttonName'])){
			$data['buttonName'] = null;
		}
		if (empty($data['promoCode'])){
			$data['promoCode'] = 'default';
		}

		if (TinkoffSettingsButton::instance()->save($data)) {
			return json_encode(['error' => 0, 'message'=>'Успешное сохранение']);
		} else {
			return  json_encode(['error' => 1, 'message'=>'Ошибка при сохранении']);
		}
	}

	public function actionTest()
	{
		$this->html->title = 'Тест Tinkoff API';
		$this->html->content = $this->render('admin/test.php');
		$this->showTemplate();
	}

	public function actionSettings()
	{
	    $data = TinkoffSettingsButton::getSettings(['getOne'=>1]);

        $this->html->title = 'Настройка подключения Кредитования Tinkoff API';
        $this->html->content = $this->render('admin/settings.php', $data);
        $this->showTemplate();
	}

	function showTemplate($layout = '@admin')
	{
		$this->html->setTemplate($layout);
		$this->html->renderTemplate()
				   ->show();
	}
}