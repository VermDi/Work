<?php
/**
 * Create by e-Mind Studio
 * User: E_dulentsov
 * Date: 18.05.2017
 * Time: 8:42
 */

namespace core;

/**
 * Class App - основной класс приложения
 *
 * @package core
 */
class App
{
	/**
	 * @var string  - в каком виде отдавать данные, если контролле вернул array or object, принимает JSON
	 */
	public static $returnType = 'JSON';
	/**
	 * @var - исполняемые контроллер
	 */
	public static $controller;
	/**
	 * @var - исполняемый экшн
	 */
	public static $action;
	/**
	 * @var - используемый модуль
	 */
	public static $module;
	/**
	 * @var -путь до модуля, заканчивается на /
	 */
	public static $ModulesPath;
	/**
	 * @var App - Действуюищй экземпляр класса App
	 */
	protected static $instance;
	/**
	 * @var - текущий урл
	 */
	public static $url;
	/**
	 * @var - урл до подмены роутами
	 */
	public static $requestUrl;
	/**
	 * @var - массив роутов и редиректов
	 */
	private $routes = [];

	public $html;

	public $auth;
	/**
	 * @var Event - события
	 */
	public $event;
	/**
	 * @var Request - запросы
	 */
	public $request;
	/**
	 * @var Errors - ошибки
	 */
	public $err;

	private $settings_exist = false;

	/**
	 * @return App - Инстанс класса
	 */
	public static function instance()
	{
		if (!isset(self::$instance)) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Указывает что настоящий урл и отображаемый равны. Если они не равны, значит или произошла подмена урла для APP
	 * или это отработало routes
	 * return bool
	 *
	 */
	public static function isSameUrl()
	{
		return (serialize(self::$url) == serialize(self::$requestUrl));
	}

	/**
	 * App constructor.
	 *
	 * @param bool $url - ссылка для загрузки класса, позволит пройти весь путь системы по указанной ссылке.
	 *                  Может оказать полезным
	 */
	protected function __construct($url = false)
	{
		$oU = new Url();
		self::$requestUrl = $oU->getUrl($_SERVER['REQUEST_URI']);
		/*
		* Прогружаем настройки.
		*/
		$this->loadSettings();
		self::$instance = $this;
		/**
		 * Если настройки не нашлись, то тянем данные иные.
		 */
		if (!$this->settings_exist) {
			$time = parse_url($_SERVER['REQUEST_URI']);
			$url = substr($url, 0, -(strlen($time['path']))) . "/install";
		}
		$oU->checkSlashOnEnd();
		/*
		 * Путь для модулей
		 */
		if (substr(_MOD_PATH_, -1) != "/") {
			self::$ModulesPath = _MOD_PATH_ . "/";
		} else {
			self::$ModulesPath = _MOD_PATH_;
		}
		$this->initRoutes();
		Event::trigger('core.routes.init', $this);
		$routes = new Routes();
		$routes->add($this->routes);
		self::$url = $oU->getUrl($routes->route(($url) ? $url : $_SERVER['REQUEST_URI']));
		Event::trigger('core.url.finded', $this);
		$this->response($this->run());

	}

	protected function response($resp)
	{
		if (is_array($resp) or is_object($resp)) {
			if (self::$returnType == 'JSON') {
				$resp = json_encode($resp);
				if (class_exists('\core\StaticCache')) {
					if (defined(StaticCache::$nameCache)) {
						(new StaticCache())->save($resp);
					}
				}
				echo $resp;
			}
		} else if (is_string($resp) && $resp != null) {
			echo $resp;
		}
	}

	/**
	 * Функция взводит приложение, для его запуска.
	 */
	protected function run()
	{
		/*
		 * Делаем вывод ошибок для девелопера
		 */
		if (_DEVELOPER_MODE_) {
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
			ini_set('display_startup_errors', 1);
		} else {
			error_reporting(0);
			ini_set('display_errors', 0);
			ini_set('display_startup_errors', 0);
		}
		/*
		 * Взводим тригеры
		 */
		$this->event = new Event();
		/*
	   * Считываем события модулей
	   */

		$this->initModules();
		/*
		 * Стартуем сессию юзвера
		 */
		Auth::startUserSession();
		/*
		 * Языки и взводим модуль
		 */
		$this->setModule();
		/*
		 * Взводим контроллер
		 */
		$this->setController();
		/*
		 * Взводим экшн
		 */
		$this->setAction();
		/*
		 * Запросы get, post, put, ajax и т.д.
		 */

		$this->request = new Request();


		/*
		* Поиск контроллера и запуск контроллера
		*/
		return $this->findController();

	}

	/**
	 * Загружем настроечный файл
	 */
	protected function loadSettings()
	{

		if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . self::$requestUrl['clear_host'] . "-defines-local.php")) {
			$this->settings_exist = true;
			include_once(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . self::$requestUrl['clear_host'] . "-defines-local.php");
			return;
		}
		if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "defines-local.php")) {
			$this->settings_exist = true;
			include_once(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "defines-local.php");
			return;
		}

		if (!require_once(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "defines.php")) {
			die('DEFINES ERROR');
		}


	}

	/**
	 * Метод просто говорит, язык дефолтовый или нет.
	 *
	 * @return bool
	 */
	public static function isDefaultLanguage()
	{
		return _CURRENT_LANGUAGE_ == _DEFAULT_LANGUGE_ ? true : false;
	}

	/**
	 * Взводим модуль
	 */
	function setModule()
	{
		if (empty(self::$url['way'])) {
			/*
			 * Дефолтный
			 */
			self::$module = _DEFAULT_MODULE_;
			define("_CURRENT_LANGUAGE_", _DEFAULT_LANGUGE_);
		} else {
			if (_MULTI_LANGUGE_) {
				$language = isset(self::$url['way'][0]) ? self::$url['way'][0] : null;
				$arrayLanguages = explode(',', _LANGUGES_);
				if (in_array($language, $arrayLanguages)) {
					define('_CURRENT_LANGUAGE_', $language);
					array_shift(self::$url['way']);
					self::$url['path'] = substr(self::$url['path'], 1 + strlen(_CURRENT_LANGUAGE_));
					if (empty(self::$url['path'])) {
						self::$url['path'] = "/";
					}
				}
			} else {
				define("_CURRENT_LANGUAGE_", _DEFAULT_LANGUGE_);
			}
			if (!defined('_CURRENT_LANGUAGE_')) {
				define("_CURRENT_LANGUAGE_", _DEFAULT_LANGUGE_);
			}
			$fs = FS::instance();
			if (isset(self::$url['way'][0]) and $fs->isFile(self::$ModulesPath . self::$url['way'][0])) {
				self::$module = self::$url['way'][0];
			} else {
				self::$module = _DEFAULT_MODULE_;
			}
		}
		Event::trigger('core.app.moduleIsSet', $this);
	}


	/**
	 * Взводим контроллер по первой части урла
	 */
	function setController()
	{
		if (empty(self::$url['way'][1])) {
			self::$controller = "index";
		} else {
			self::$controller = self::$url['way'][1];
		}
		self::$controller = ucfirst(self::$controller);
		Event::trigger('core.app.controllerIsSet', $this);
	}

	/**
	 * Взводим экшн (действие)
	 */
	function setAction()
	{
		if (empty(self::$url['way'][2])) {
			self::$action = "index";
		} else {
			self::$action = self::$url['way'][2];
		}
		Event::trigger('core.app.actionIsSet', $this);
	}


	/**
	 * Регистрация системы хуков (реально же считывает события из всех имеющихся модулей)
	 */
	private function initModules()
	{
		$fs = FS::instance();
		foreach (glob(self::$ModulesPath . '*', GLOB_ONLYDIR | GLOB_MARK) as $dir) {
			if ($fs->isFile($file = $dir . 'boot.php')) {
				include_once($file);
			}
		}
	}

	/**
	 * Регистрация системы хуков (реально же считывает события из всех имеющихся модулей)
	 */
	private function initRoutes()
	{
		$fs = FS::instance();
		foreach (glob(self::$ModulesPath . '*', GLOB_ONLYDIR | GLOB_MARK) as $dir) {
			if ($fs->isFile($file = $dir . 'routes.php')) {
				$this->routes = $this->routes + include_once($file);
			}
		}
	}

	/**
	 * Поиск и запуск контроллера
	 */
	protected function findController()
	{
		$fs = FS::instance();
		$dir = self::$ModulesPath . self::$module . DIRECTORY_SEPARATOR . "controllers";
		$dir_extends = self::$ModulesPath . _BASE_DOMAIN_ . DIRECTORY_SEPARATOR . "controllers";
		$moduleName = self::$module;
		$controllerName = false;

		/*
		 * Ищем контроллер
		 */
		if ($fs->isFile($dir . DIRECTORY_SEPARATOR . self::$controller . ".php") or $fs->isFile($dir_extends . DIRECTORY_SEPARATOR . self::$controller . ".php")) {
			$controllerName = self::$controller;
		} elseif ($fs->isFile($dir . DIRECTORY_SEPARATOR . self::$controller . ".php") or $fs->isFile($dir_extends . DIRECTORY_SEPARATOR . self::$controller . ".php")) {
			$controllerName = self::$controller;
		} elseif ($fs->isFile($dir . DIRECTORY_SEPARATOR . "index.php") or $fs->isFile($dir_extends . DIRECTORY_SEPARATOR . "index.php")) {
			$controllerName = 'index';
		} elseif ($fs->isFile($dir . DIRECTORY_SEPARATOR . "Index.php") or $fs->isFile($dir_extends . DIRECTORY_SEPARATOR . "Index.php")) {
			$controllerName = 'Index';
		} elseif ($fs->isFile(_MOD_PATH_ . DIRECTORY_SEPARATOR . _DEFAULT_MODULE_ . DIRECTORY_SEPARATOR . "controllers" . DIRECTORY_SEPARATOR . "index.php")) {
			$moduleName = _DEFAULT_MODULE_;
			$controllerName = 'index';
		} elseif ($fs->isFile(_MOD_PATH_ . DIRECTORY_SEPARATOR . _DEFAULT_MODULE_ . DIRECTORY_SEPARATOR . "controllers" . DIRECTORY_SEPARATOR . "Index.php")) {
			$moduleName = _DEFAULT_MODULE_;
			$controllerName = 'Index';
		} else {
			Errors::criticalEror('ОШИБКА! <br>НЕ СМОГ НАЙТИ КОНТРОЛЛЕР! Для  ' . self::$module . '/' . self::$controller . '<br> ФАЙЛ:' . __FILE__ . " <br> СТРОКА: " . __LINE__);
		}
		/*
		 * Проверям права доступа к контроллеру
		 */
		if (!Rights::instance()->checkAccess($moduleName, $controllerName)) {
			if (\modules\user\models\USER::isAuthorized()) {
				Errors::criticalEror('Недостаточно прав');
				exit;
			} else {
				if (App::$url['path'] != '/user/login') {
					header("Location: /user/login?redirectUrl=" . $_SERVER['REQUEST_URI']);
					die();
				} else {
					Errors::criticalEror('Недостаточно прав');
				}
				exit;
			}
		}

		if (defined("_BASE_DOMAIN_") and $fs->isFile(_MOD_PATH_ . DIRECTORY_SEPARATOR . $moduleName . DIRECTORY_SEPARATOR . _BASE_DOMAIN_ . DIRECTORY_SEPARATOR . $controllerName . '.php')) {
			$fs->includeFile(_MOD_PATH_ . DIRECTORY_SEPARATOR . $moduleName . DIRECTORY_SEPARATOR . _BASE_DOMAIN_ . DIRECTORY_SEPARATOR . $controllerName . '.php');
			if (class_exists($controllerClass = '\\modules\\' . self::$module . '\\' . _BASE_DOMAIN_ . '\\' . $controllerName)) {
				if (is_subclass_of($controllerClass, '\core\Controller')) {
					$controller = new $controllerClass();
					/**
					 * @var $controller Controller
					 */
					return $controller->run();
				};
			}
		} else {
			$fs->includeFile(self::$ModulesPath . $moduleName . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $controllerName . '.php');
			if (class_exists($controllerClass = '\\modules\\' . self::$module . '\\controllers\\' . $controllerName)) {
				if (is_subclass_of($controllerClass, '\core\Controller')) {
					$controller = new $controllerClass();
					/**
					 * @var $controller Controller
					 */
					return $controller->run();
				};
			} else {
				Errors::e404();
			}
		}
	}

	/**
	 * v 1.0 - 28.03.2017
	 * Метод принимает на входе один из трех вариантов:
	 * 1. Указан только первый передаваемый параметр Модуль, в этом случае проверяю только наличие модуля
	 * 2. Указан только второй параметр, первый false, проверяет наличие класса по полному пути неймспейса
	 * 3. Передены оба параметра - модуль, и название класса (как просто название) - проверит наличие класса
	 *  в моделях и контроллерах и вернут true если хоть где то есть.
	 *
	 * @param bool $module - название модуля
	 * @param bool $class  - нэймспецс класса - как полный путь, либо просто имя класса, но в связке с именем модуля
	 *
	 * @return bool - возвращает true or false
	 */
	public static function IsModuleExists($module = false, $class = false)
	{
		if ($module != false and $class == false) {
			$path = __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR . $module;
			if (file_exists($path)) {
				return true;
			}
		}
		if ($class != false and $module == false) {
			if (class_exists($class)) {
				return true;
			}
		}
		if ($class != false and $module != false) {
			// if (class_exists("\\modules\\newstoshop\\models\\NTS")
			$classm_name = "\\" . modules . "\\" . $module . "\\" . "models" . "\\" . $class;
			$classc_name = "\\" . modules . "\\" . $module . "\\" . "controllers" . "\\" . $class;
			if (class_exists($classc_name) or class_exists($classm_name)) {
				return true;
			}

		}

		return false;

	}

	/**
	 * Вернет ответ на вопрос, это главная страница ?
	 *
	 * @return bool
	 */
	public static function isMainPage()
	{
		if (self::$url['path'] == '/') {
			return true;
		}
		return false;
	}

	/**
	 * Will be moved in future to anover class
	 */
	private function arrayChangeKeyCaseRecursive($arr)
	{
		return array_map(
			function ($item) {
				if (is_array($item)) {
					$item = $this->arrayChangeKeyCaseRecursive($item);
				}
				return $item;
			}, array_change_key_case($arr)
		);
	}
}