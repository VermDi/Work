<?php

namespace core;
/**
 * Простой класс для визуализации шаблонов. По сути это хелпер.
 * Class Html
 *
 * @package core
 */
class Html extends Obj
{

	/**
	 * Заголово страницы
	 *
	 * @var
	 */
	public $title;
	/**
	 * Переменная которая хранит мета данные
	 *
	 * @var
	 */
	public $meta;
	/**
	 * Все содиржимое центральной части экрана. Как правило является составной переменной.
	 *
	 * @var
	 */
	public $content;
	/**
	 * Содерижт код и спсок подключаемых файлов css
	 *
	 * @var
	 */
	private static $css;
	/**
	 * Содерижт код и спсок подключаемых файлов JS
	 *
	 * @var
	 */
	private static $js = [];
	/**
	 * Файл шаблона который будет рендериться
	 *
	 * @var
	 */
	private $file;
	/**
	 * Пукть к файлу осноновного шаблона
	 *
	 * @var
	 */
	private $template = null;
	/**
	 * Возвращается ранее созданный экземпляр класса
	 *
	 * @var
	 */
	protected static $instance;
	/**
	 * Содержит отрендеренный шаблон
	 *
	 * @var
	 */
	public $render;
	public $all_ready = [];
	/**
	 * Переменная содержит массив отрисованных элементов
	 *
	 * @private
	 */
	private $rendered = [];
	/**
	 * @var bool Метка говорит о том, что вставка должна быть первой.
	 */
	private $isToTop = false;
	/**
	 * @var bool говорит показывать виджеты или нет, по умолчанию TRUE
	 */
	private $isShowWidgets = true;

	/**
	 * @return Html|Obj Инстанс класса
	 */
	public static function instance()
	{
		if (!isset(self::$instance)) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Передаем либо путь к файлу css либо сам стиль, но в этом случае нужно взвести метку inline. Content - обязательный аргумент
	 *
	 * @param string $content
	 * @param bool $inline
	 *
	 * @return Html
	 * @throws \Exception
	 */
	public function setCss($content = "", $inline = false)
	{
		if (is_array($content)) {
			foreach ($content as $item) {
				self::instance()->setCss($item);
			}
		} else {
			$result = false;
			if (!in_array($content, $this->all_ready)) {
				$this->all_ready[] = $content;
				if ($content != "") {
					if ($inline) {
						if (file_exists($content)) {
							$content = file_get_contents($content);
						} else {
							self::$css['style'][] = $content;
						}
					} else {
						foreach ((array)$content as $src) {
							self::$css['links'][] = $src;
						}
					}
					$result = true;
				}
			} else {
				$result = true;
			}
			if ($result != true) {
				throw new \Exception('НЕ смог подключить CSS');
			}
			return $this;
		}
	}

	private function linksToAbsolute($txt, $path)
	{
		//preg_match_all();
		$path = pathinfo($path)['dirname'];
		$oneLevel = dirname($path);
		$secondLevel = dirname($oneLevel);
		$txt = str_replace("../..", $secondLevel, $txt);
		$txt = str_replace("..", $oneLevel, $txt);
		$txt = str_replace("\"./", "\"" . $path . "/", $txt);


		return $txt;
	}

	/**
	 * Метод превращает набор в один файл, контроль идет по перечню файлов. Сами файлы не проверяются пока!
	 *
	 * @return string
	 */
	private function cssToFile()
	{
		$file = md5(json_encode(self::$css));
		$wFile = $_SERVER['DOCUMENT_ROOT'] . "/public/cache/css/" . $file . ".css";
		if (!file_exists($wFile) and empty($_GET['clear_cache'])) {
			if (FS::instance()->createFolder($_SERVER['DOCUMENT_ROOT'] . "/public/cache/css/")) {
				$fch = fopen($wFile, 'w');
				if (isset(self::$css['style'])) {
					fwrite($fch, " \r\n\r\n /* --- INLINE BLOCK ---- */ \r\n\r\n" . $this->minimizeCSS(self::$css['style']));
				}
				if (isset(self::$css['links'])) {
					foreach (self::$css['links'] as $link) {
						if (file_exists($_SERVER['DOCUMENT_ROOT'] . $link)) {
							fwrite($fch, " \r\n\r\n /* --- " . $link . " ---- */ \r\n\r\n" . $this->minimizeCSS($this->linksToAbsolute(file_get_contents($_SERVER['DOCUMENT_ROOT'] . $link), $link)));
						} else {
							fwrite($fch, " \r\n\r\n /* --- " . $link . " НО ФАЙЛА НЕТ!!! ---- */ \r\n\r\n");
						}
					}
				}
				fclose($fch);
			}
		}
		return $file;
	}

	/**
	 * Метод сливает js в кэш файл! При этом учитывает что это в шапке или футере.
	 *
	 * @param bool $inHead
	 *
	 * @return string
	 */
	private function jsToFile($inHead = false)
	{
		if ($inHead) {
			$arr = self::$js['inHead'];
		} else {
			$arr = self::$js['inFooter'];
		}
		$file = md5(json_encode($arr));
		$wFile = $_SERVER['DOCUMENT_ROOT'] . "/public/cache/js/" . $file . ".js";
		if (!file_exists($wFile) and empty($_GET['clear_cache'])) {
			if (FS::instance()->createFolder($_SERVER['DOCUMENT_ROOT'] . "/public/cache/js/")) {
				$fch = fopen($wFile, 'w');
				if (isset(self::$js[''])) {
					fwrite($fch, " \r\n\r\n /* --- INLINE BLOCK ---- */ \r\n\r\n" . $this->minifyJavascriptCode(implode(" ", $arr['src'])));
				}
				if (isset($arr['file'])) {
					foreach ($arr['file'] as $link) {
						fwrite($fch, " \r\n\r\n /* --- " . $link . " ---- */ \r\n\r\n" . $this->minifyJavascriptCode(file_get_contents($_SERVER['DOCUMENT_ROOT'] . $link)));
					}
				}
				fclose($fch);
			}
		}
		return "/public/cache/js/" . $file . ".js";
	}

	/**
	 * @return $this
	 * @example Html::instance()->toTop()->setJs('PATH_TO_FILE')->stopToTop();
	 *
	 * Метод взводит метку, чтобы вставка была первой в списке
	 *
	 */
	public function toTop()
	{
		$this->isToTop = -1;
		return $this;
	}

	/**
	 * Снимаент метку добавления в вверх массива
	 *
	 * @return $this
	 * @example Html::instance()->stopToTop();
	 */
	public function stopToTop()
	{
		$this->isToTop = false;
		return $this;
	}

	/**
	 * Метод принимает строку и минифицирует её
	 *
	 * @param $javascript
	 *
	 * @return null|string|string[]
	 */
	private function minifyJavascriptCode($javascript)
	{
		return str_replace(["    ", "  "], " ", trim($javascript));
	}

	/**
	 * Метод минифицирует css
	 *
	 * @param $css
	 *
	 * @return null|string|string[]
	 */
	private function minimizeCSS($css)
	{
		if (is_array($css)) {
			$css = implode(" ", $css);
		}
		$css = preg_replace('/\/\*((?!\*\/).)*\*\//', '', $css); // negative look ahead
		$css = preg_replace('/\s{2,}/', ' ', $css);
		$css = preg_replace('/\s*([:;{}])\s*/', '$1', $css);
		$css = preg_replace('/;}/', '}', $css);
		return $css;
	}

	/**
	 * Отрисовывает все зарегистрированные с помощью функции setCss стили
	 *
	 * @example <?php Html::instance()->showCss(); ?>
	 *
	 * @param bool $cache - при true минифицирует CSS файл, и записывается все в один файл
	 */
	public function showCss($cache = false)
	{
		if ($cache == true) {
			$link = "public/cache/css/" . $this->cssToFile() . ".css";
			echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"/" . $link . "?" .
				filemtime($_SERVER['DOCUMENT_ROOT'] . "/" . $link) . "\">";
		} else {

			if (isset(self::$css['style'])) {
				echo "<style type=\"text/css\">";
				echo implode("", self::$css['style']);
				echo "</style>";
			}
			if (isset(self::$css['links'])) {
				foreach (self::$css['links'] as $link) {
					$time = "";
					if (file_exists($_SERVER['DOCUMENT_ROOT'] . $link)) {
						$time = filemtime($_SERVER['DOCUMENT_ROOT'] . $link);
					}
					echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"$link?" . $time . "\">";
				}
			}
		}
	}

	/**
	 * Единая функция для вывода и записи css
	 *
	 * @param false $content
	 * @param false $inline
	 *
	 * @throws \Exception
	 */
	public function css($content = false, $inline = false)
	{
		if ($content) {
			$this->setCss($content, $inline);
		} else {
			$this->showCss();
		}

	}

	/**
	 * Позволяет зарегистрировать js или вывести в футере
	 *
	 * @param false $content
	 * @param false $inhead
	 * @param false $inline
	 *
	 * @throws \Exception
	 */
	public function js($content = false, $inhead = false, $inline = false)
	{
		if ($content) {
			$this->setJs($content, $inhead, $inline);
		} else {
			$this->showJs();
		}

	}

	/**
	 * Возвращает массив зарегистрированных CSS
	 *
	 * @return
	 */
	public function getCss()
	{
		return self::$css;
	}

	/**
	 *
	 * Добавляем скрипт
	 * content - обязательный аргумент
	 * если inhead == true, то располагается в заголовке документа, иначе располагается в конце тела документа
	 * если inline == true, то content - тело скрипта, иначе content - ссылка на файл скрипта
	 *
	 * @param string $content
	 * @param bool $inhead
	 * @param bool $inline
	 *
	 * @return Html
	 * @throws \Exception
	 */
	public function setJs($content = "", $inhead = false, $inline = false)
	{
		if (is_array($content)) {
			foreach ($content as $item) {
				self::instance()->setJs($item);
			}
		} else {

			if (!in_array($content, $this->all_ready)) {
				$this->all_ready[] = $content;
				$result = false;
				if (!empty($content)) {
					if ($inhead) {
						if ($inline) {
							if (!isset(self::$js['inHead']['src'])) {
								self::$js['inHead']['src'] = [];
							}
							if ($this->isToTop !== false) {
								$this->isToTop++;
								array_splice(self::$js['inHead']['src'], $this->isToTop, 0, $content);
								//array_unshift(self::$js['inHead']['src'], $content);
							} else {
								array_push(self::$js['inHead']['src'], $content);
							}
						} else {
							if (!isset(self::$js['inHead']['file'])) {
								self::$js['inHead']['file'] = [];
							}
							if ($this->isToTop !== false) {
								$this->isToTop++;
								array_splice(self::$js['inHead']['file'], $this->isToTop, 0, $content);
								//array_unshift(self::$js['inHead']['file'], $content);
							} else {
								array_push(self::$js['inHead']['file'], $content);
							}
						}
					} else {
						if ($inline) {
							if (strlen($content) < 250 and file_exists($content)) {
								$content = file_get_contents($content);
							}
							if (!isset(self::$js['inFooter']['src'])) {
								self::$js['inFooter']['src'] = [];
							}
							if ($this->isToTop !== false) {
								$this->isToTop++;
								array_splice(self::$js['inFooter']['src'], $this->isToTop, 0, $content);
								// array_unshift(self::$js['inFooter']['src'], $content);
							} else {
								array_push(self::$js['inFooter']['src'], $content);
							}

						} else {
							if (!isset(self::$js['inFooter']['file'])) {
								self::$js['inFooter']['file'] = [];
							}
							if ($this->isToTop !== false) {
//                            var_dump($this->isToTop);
//                            print_r(self::$js['inFooter']['file']);
								$this->isToTop++;
								array_splice(self::$js['inFooter']['file'], $this->isToTop, 0, $content);
								// array_unshift(self::$js['inFooter']['file'], $content);
							} else {
								array_push(self::$js['inFooter']['file'], $content);
							}
						}
					}
					$result = true;
				}
				if ($result != true) {
					throw new \Exception('НЕ смог подключить CSS');
				}
				return $this;
			} else {
				return $this;
			}
		}
	}

	/**
	 * Функция вставляет в тело письма ранее подключенный JS
	 *
	 * @example <?php Html::instance()->showJs(true); //выведет включения для шапки, по очереди?>
	 * @example <?php Html::instance()->showJs();  //выведет включения для футераб по очереди ?>
	 * @example <?php Html::instance()->showJs(false, true); //выведет все включения для футера в виде кэша ?>
	 * @example <?php Html::instance()->showJs(true, true); //выведет все включения для шапки в виде кэша ?>
	 *
	 * @param bool $head
	 * @param bool $cache
	 * */
	public function showJs($head = false, $cache = false)
	{
		if ($head) {
			$arr = isset(self::$js['inHead']) ? self::$js['inHead'] : false;
		} else {
			$arr = isset(self::$js['inFooter']) ? self::$js['inFooter'] : false;
		}
		if (!$cache) {
			if (isset($arr['src']) and is_array($arr['src'])) {
				echo "<script language=\"JavaScript\" type=\"text/javascript\">\n";
				foreach ($arr['src'] as $k => $line) {
					echo "<!--\n" . $line . "//-->\n";
				}
				echo "</script>\n";
			}
			if (isset($arr['file']) and is_array($arr['file'])) {
				foreach ($arr['file'] as $k => $line) {
					if (file_exists($_SERVER['DOCUMENT_ROOT'] . $line)) {
						echo "<script src=\"" . $line . "?" . @filemtime($_SERVER['DOCUMENT_ROOT'] . $line) . "\"></script>\n";
					} else {
						if (strtolower(substr($line, 0, 4)) == 'http' or substr($line, 0, 2) == '//') {
							echo "<script src=\"" . $line . "\"></script>\n";
						} else {
							echo "<!-- здесь должен был быть файл " . $line . " но его почемуто нет -->";
						}
					}
				}
			}
		} else {
			echo "<script src=\"" . $this->jsToFile($head) . "\"></script>\n";

		}
	}

	/**
	 * @return array
	 */
	public function getJs()
	{
		return self::$js;
	}

	/**
	 * Устанавливаем содержимое контента, для замкнутых цепочек
	 *
	 * @param $content
	 *
	 * @return $this
	 */
	function setContent($content)
	{
		$this->content = $content;
		return $this;
	}

	/**
	 * Устанавливает файл для рендернинга
	 *
	 * @param $file
	 *
	 * @return $this
	 */

	function setFile($file)
	{
		$this->file = $file;
		return $this;
	}

	/**
	 * Устанавливает шаблон рендеринга, @шаблон - системные, шаблон - пользовательские
	 *
	 * @param $template
	 *
	 * @return $this
	 */
	function setTemplate($template)
	{
		$this->template = $template;
		return $this;
	}

	/**
	 * Возвращает действующий шаблон
	 *
	 * @return string|null
	 */
	public function getTemplate()
	{
		return $this->template;
	}

	/**
	 * Ренедрит центральный шаблон
	 *
	 * @param bool|string $tmpl - @admin or index
	 *
	 * @return $this
	 */
	function renderTemplate($tmpl = false)
	{
		Event::instance()->trigger('core.template.render', $this);
		if (!empty($tmpl)) {
			$this->template = $tmpl;
		}
		if ($this->template == null and defined('_DEFAULT_TEMPLATE_')) {
			$this->template = _DEFAULT_TEMPLATE_;
		}
		if (substr($this->template, 0, 1) == "@") {
			$folder = "system";
			$template = substr($this->template, 1);
		} else {
			$folder = "front";
			$template = $this->template;
		}
		$file = _SYS_PATH_ . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $template . DIRECTORY_SEPARATOR . $template . ".php";
		// Если шаблон не найден, то ставим по умолчанию
		if (!FS::instance()->isFile($file)) {
			$template = _DEFAULT_TEMPLATE_;
		}
		$file = _SYS_PATH_ . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $template . DIRECTORY_SEPARATOR . $template . ".php";
		if (FS::instance()->isFile($file)) {
			$this->render($file);
		} else {
			die($file);
		}
		Event::instance()->trigger('core.template.rendered', $this);
		return $this;
	}

	/**
	 * Рендерит указанный файл с данными указанными в data
	 *
	 * @param $file
	 * @param array|bool $data
	 *
	 * @return string
	 */
	function render($file, $data = [])
	{
		ob_start();
		ob_implicit_flush(false);
		if (!FS::instance()->includeFile($file, $data) and _DEVELOPER_MODE_ == true) {
			Errors::e500("Не удалось подлючить темлейт: " . $file);
		} else {
			$this->rendered[] = ['file' => $file, 'data' => $data];
		}
		$render = ob_get_contents();
		ob_end_clean();

		return $this->render = $render;

	}

	/**
	 * Выводит на экран отрендеренный шаблон
	 */
	function show()
	{

		Event::trigger('core.template.show', $this);
		$this->loadWidgets();
		if (defined("SUPERCACHE")) {
			if (class_exists("core\StaticCache")) {
				(new StaticCache())->save($this->render);
			}
		}
		echo $this->render;

		Event::trigger('core.template.shown', $this);
	}

	/**
	 * Проверяет, является ли вызванный темплейт, административным.
	 *
	 * @return bool
	 */
	public function isSystemTemplate()
	{
		return substr($this->template, 0, 1) == "@";
	}

	/**
	 * Возвращает все отренедеренные шаблоны в виде массива элементов
	 *
	 * @return array $this->rendered
	 */

	public function getRendered()
	{
		return $this->rendered;
	}

	/**
	 * Запрет на отрисовку виджетов
	 */
	public function setNoWidgets()
	{
		$this->isShowWidgets = false;
	}

	/**
	 * Ставит флаг что отображать виджеты
	 */
	public function setShowWidgets()
	{
		$this->isShowWidgets = true;
	}

	/**
	 * Метод выводи виджеты, метод облегчает подставноку виджетов, но замедляет работу сайта
	 * пример вызова {widget:picture.Show.getPath.555*jpg}
	 */

	public function loadWidgets()
	{
		$widgets = [];
		preg_match_all('/{widget:.*?}/', $this->render, $matches);

		foreach ($matches[0] as $match) {
			$widgets[$match] = substr($match, 8, -1);
		}
		$widgets = array_unique($widgets);

		foreach ($widgets as $k => $v) {
			$w = explode(".", $v);
			if (class_exists("\\modules\\" . $w[0] . "\\widgets\\" . $w[1])) {
				$class = "\modules\\" . $w[0] . "\widgets\\" . $w[1];
				$method = $w[2];
				if (method_exists($class, $method)) {
					$params = "";
					if (isset($w[3])) {
						$params = $w[3];
					}
					//print_r( $this->render);
					$params_exploded = explode("*", $params);
					if (count($params_exploded) === 1) {
						$this->render = str_replace($k, $class::$method($params_exploded[0]), $this->render);
					}
					if (count($params_exploded) === 2) {
						$txt = $this->render;
						$txt = $class::$method($params_exploded[0], $params_exploded[1]);
						$this->render = str_replace($k, $txt, $txt);
					}
					if (count($params_exploded) === 3) {
						$this->render = str_replace($k, $class::$method($params_exploded[0], $params_exploded[1], $params_exploded[2]), $this->render);
					}
				}
			}
		}
	}

	/**
	 * Вызов метода в шаблоне позволит разметить блок с метриками
	 *
	 * @return string
	 *
	 *
	 */
	public static function Metrics()
	{
		if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/metrics.txt")) {
			return file_get_contents($_SERVER['DOCUMENT_ROOT'] . "/metrics.txt");
		} else {
			return "";
		}
	}

	/**
	 * Санизация в шаблоне
	 *
	 * @param $val
	 *
	 * @return string
	 */
	public function sanitize($val)
	{
		return htmlspecialchars($val);
	}

	/**
	 * Метод печатает атрибут выбора для поля select в options
	 *
	 * @param $val1
	 * @param $val2
	 *
	 * @return void
	 *
	 * @example core\Html::printSelected($fieldVal, $data->val);
	 */
	public static function printSelected($val1, $val2)
	{
		if ($val1 == $val2) {
			echo "selected=\"selected\"";
		}

	}
}