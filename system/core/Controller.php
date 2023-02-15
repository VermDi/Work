<?php

namespace core;

use Exception;

abstract class Controller extends Obj
{

	protected static $nestingLevel = 1;
	public           $model;
	public           $form_template;
	public           $list_template;
	public           $url;
	public           $config;
	/**
	 * @var Controller
	 */
	public $parent;
	/**
	 * @var Controller
	 */
	public $root;
	/**
	 * @var array разрешенные методы запроса
	 */
	protected $methods = [
		'GET',
		'POST',
		'PUT',
		'DELETE',
		'AJAX',
		'HEAD',
	];
	/**
	 * @var array параметры из url
	 */
	protected $parameters;
	/**
	 * @var string действие, которое необходимо выполнить, определяется при разборе url
	 */
	protected $action;
	const DEFAULT_ACTION    = 'index';
	const ANY_METHOD_PREFIX = 'action';

	function __construct($model = null)
	{
		if (file_exists($this->getModulePath() . "/set.php")) {
			$this->config = include($this->getModulePath() . "/set.php");
		} elseif (!$this->config = Parameters::getAsArray($this->getModuleName())
		) {
			$this->config = false;
		}
		parent::__construct();
		$this->model = $model;
	}

	/**
	 * Синоним для получения настроек модуля
	 *
	 * @return bool
	 */
	public function settings()
	{
		return $this->config;
	}

	public function run()
	{

		$this->parseUrl();
		if ($childController = $this->findChildController()) {
			self::$nestingLevel += 1;
			$controller = new $childController();
			$controller->parent = $this;
			$controller->root = self::$nestingLevel == 1 ? $this : $this->parent;
			/**
			 * @var $controller Controller
			 */
			return $controller->run();
		} else {
			try {
				return $this->runAction($this->action);
			} catch (Exception $e) {
				if ($e->getCode() == 404) {
					Errors::e404();
				} else {
					if (_DEVELOPER_MODE_) {
						Errors::e500(false, $e);
					} else {
						Errors::criticalEror($e->getMessage());
					}
				}
			}
		}
	}

	protected function findChildController()
	{
		$out = null;
		$parameters = $this->parameters;
		$childModuleName = $this->action;
		$childModuleNamespace = $this->getModuleNamespace() . '\\modules\\' . $childModuleName;
		$childModuleControllerNamespace = $childModuleNamespace . '\\controllers';
		$childModuleControllerClass = $childModuleControllerNamespace . '\\' . (ucfirst(array_shift($parameters)) ?: 'Index');
		$childModuleIndexControllerClass = $childModuleControllerNamespace . '\\Index';
		if (class_exists($childModuleControllerClass)) {
			$out = $childModuleControllerClass;
		} elseif (class_exists($childModuleIndexControllerClass)) {
			$out = $childModuleIndexControllerClass;
		}

		return $out;
	}

	protected function getModuleNamespace()
	{
		return implode('\\', array_slice(explode('\\', get_class($this)), 0, -2));
	}

	protected function render($file, $data = [])
	{
		$viewPath = _SYS_PATH_ . DIRECTORY_SEPARATOR . dirname(dirname(str_replace('\\', DIRECTORY_SEPARATOR, get_class($this)))) . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . $file;
		$viewPathNew = _SYS_PATH_ . DIRECTORY_SEPARATOR . dirname(dirname(str_replace('\\', DIRECTORY_SEPARATOR, get_class($this)))) . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $file;
		if (!file_exists($viewPath) && !file_exists($viewPathNew)) {

			throw new Exception("Представление $viewPath не найдено");
		}
		if (file_exists($viewPathNew))
			return Html::instance()->render($viewPathNew, $data);
		if (file_exists($viewPath))
			return Html::instance()->render($viewPath, $data);
	}

	/**
	 * Render only module template, or render module template in system template.
	 *
	 * @param string $template
	 * @param string $type
	 * @param array $data
	 */
	public function renderTemplate($template, $type, $data = [])
	{
		if (!empty($currentMainTemplate)) {
			Html::instance()->content = $this->render($template . ".php", $data);
			Html::instance()->setTemplate($currentMainTemplate);
			Html::instance()->renderTemplate()->show();
		} else {
			echo $this->render($template . ".php", $data);
		}
	}

	protected function getModulePath()
	{
		return _SYS_PATH_ . DIRECTORY_SEPARATOR . dirname(dirname(str_replace('\\', DIRECTORY_SEPARATOR, get_class($this))));
	}

	protected function getModuleName($glue = DIRECTORY_SEPARATOR)
	{
		$name = implode(
			$glue, array_filter(
				array_slice(explode(DIRECTORY_SEPARATOR, $this->getModulePath()), -2 * self::$nestingLevel), function ($val) {
				return $val != 'modules';
			}
			)
		);
		return $name;
	}

	protected function getControllerName()
	{
		return basename(str_replace('\\', DIRECTORY_SEPARATOR, get_class($this)));
	}

	/**
	 * Выполняет указанный action и передает ему параметры из url. Недостающие параметры будут заменены null
	 *
	 * @param string $action
	 *
	 * @return mixed
	 * @throws \Exception
	 */
	protected function runAction($action = self::DEFAULT_ACTION)
	{
		if (!in_array(strtoupper($this->app->request->method()), $this->methods)) {
			throw new Exception("Method Not Allowed");
		}
		if (!$method = $this->findMethod($action)) {
			if ($method = $this->findMethod()) {
				array_unshift($this->parameters, $this->action);
				$this->action = self::DEFAULT_ACTION;
			} else {
				throw new Exception("Not Found");
			}
		}
		if (!$this->allowed($this->action)) {
			throw new Exception("Forbidden");
		}
        $r = new \ReflectionMethod($this, $method);
		$p = $r->getParameters();
		if (isset($p[0]) && $p[0]->isDefaultValueAvailable() && $p[0]->getDefaultValue()=="*") {

		} else {
			if (count($this->parameters) < $r->getNumberOfRequiredParameters() || count($this->parameters) > $r->getNumberOfParameters()) {
				throw new Exception("Not Found"); // чтобы страницы 2 уровня работали /services/analytics
			}
		}
		$result = call_user_func_array(
			[
				$this,
				$method,
			], $this->parameters
		);
		return $result;
	}

	/**
	 * Ищет подходящий метод для действия
	 *
	 * @param string $action
	 *
	 * @return bool|string
	 */
	protected function findMethod($action = self::DEFAULT_ACTION)
	{
		$class = get_class($this);
		if (!is_callable(
			[
				$class,
				$method = $this->app->request->method() . $action,
			]
		)
		) {
			if (!is_callable(
				[
					$class,
					$method = self::ANY_METHOD_PREFIX . $action,
				]
			)
			) {
				return false;
			}
		}
		return $method;
	}

	/**
	 * Разбор url: определение экшена, параметров и сохранение их в свойствах контроллера
	 */
	protected function parseUrl()
	{
		$this->parameters = isset(App::$url['way']) ? App::$url['way'] : [];
		$this->parameters = array_slice($this->parameters, self::$nestingLevel); // убрать первые n параметров - имя модуля;
		$this->getControllerName() == 'Index' || array_shift($this->parameters); // убрать второй параметр - контролер, если это не индекс контролер;
		$this->action = array_shift($this->parameters) ?: self::DEFAULT_ACTION;
	}

	/**
	 * проверка прав
	 *
	 * @param $action
	 *
	 * @return bool
	 */
	protected function allowed($action)
	{
		return Rights::instance()->checkAccess(
			$this->getModuleName(), $this->getControllerName(), $action
		);
	}

	/**
	 * Редирект
	 *
	 * @param $url
	 */
	protected function redirect($url)
	{
		header("Location: $url");
		exit;
	}

	public function url($add = '')
	{
		return '/' . str_replace('\\', '/', $this->getModuleName()) . ($add ? "/$add" : '');
	}

	/**
	 * Выводит полученные данные в Json
	 *
	 * @param $data
	 */
	public function returnJson($data)
	{
		echo json_encode($data);
	}

	/**
	 * Редирект на прошлый урл.
	 */
	public function goBack()
	{
		$this->redirect($_SERVER['HTTP_REFERER']);
		die();
	}
}
