<?php

namespace core;

class StaticCache
{
	public static $nameCache = "SUPERCACHE";
	private       $cacheDir;
	protected     $name;
	public        $fullCacheFilePath;
	protected     $fullCacheDirPath;

	/**
	 * StaticCache constructor.
	 *
	 * @param $cacheDir
	 */
	public function __construct()
	{
		$this->cacheDir = __DIR__ . "/../cache";
		$string = "";
		if (isset($_POST) && !empty($_POST)) {
			$time_string = "";
			foreach ($_POST as $k => $v) {
				if (is_array($v) && !empty($v)) {
					if (count($v) == count($v, COUNT_RECURSIVE)) {
						$time_string .= implode("#", $v);
					}
				}
				if (is_string($v)) {
					$time_string .= $v;
				}
			}
			$string = md5($time_string);
		}

		$this->name = md5($_SERVER['REQUEST_URI'] . $string);
		$url = parse_url($_SERVER['REQUEST_URI']);
		$str = implode("/", array_diff(explode("/", $url['path']), ['']));
		$this->deleteAllCache($url['path']);
		$this->fullCacheDirPath = $this->cacheDir . "/" . $str . "/" . implode("/", str_split($this->name, 16)) . "/";
		$this->fullCacheFilePath = $this->fullCacheDirPath . $this->name;

	}

	public function deleteAllCache($path)
	{

		if ($path == "/supercache/clearallcache") {
			echo "CLEAR";
			$this->clear("");
			die();
		}
	}

	/**
	 * Включить кэш
	 */
	public static function on()
	{
		define(self::$nameCache, 1);
	}

	/**
	 * Создание кэша, на основе урла
	 *
	 * @param $requestPath
	 * @param $content
	 */
	public function save($content)
	{
		if (defined(self::$nameCache)) {
			if (!file_exists($this->fullCacheDirPath)) {
				mkdir($this->fullCacheDirPath, 0775, true);
			}
			file_put_contents($this->fullCacheFilePath, $content);
		}
	}

	protected function rrmdir($src)
	{
		if (file_exists($src)) {
			$dir = opendir($src);
			while (false !== ($file = readdir($dir))) {
				if (($file != '.') && ($file != '..')) {
					$full = $src . '/' . $file;
					if (is_dir($full)) {
						$this->rrmdir($full);
					} else {
						unlink($full);
					}
				}
			}
			closedir($dir);
			rmdir($src);
		}
	}

	/**
	 * Удалить весь кэш, если не передан путь, и по пути если передан
	 */
	public function clear($path = "")
	{
		$this->rrmdir($this->cacheDir . $path);
	}
}


