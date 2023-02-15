<?php

namespace core;

/**
 * Class FS - основная задача это обработка файлов. Со временем будет расширен для работы с ftp и другим хранилищами.
 * Многие функции повторяют функции php, с существенным отличием, что происходит регистрация подклчюаемого файла, проводятся проверки
 * на существование и не вызывает критичных ошибок и варингов. Все ошибки обрабатываются в функциях.
 *
 * @package   core
 * @copyright 2016 E-mind
 * */
class FS
{
	/**
	 * Масссив с ошибками
	 *
	 * @var array
	 */
	private static $error = [];
	/**
	 * Массив подключенных файлов.
	 *
	 * @var array
	 */
	private static   $stack = [];
	protected static $instance;
	/**
	 * Путь до обрабатываемой папки
	 *
	 * @var
	 */
	public $folder;
	/**
	 * Путь до файла обрабатываемого
	 *
	 * @var
	 */
	public        $files  = [];
	public static $all    = [];
	private       $images = [ // images
							  'png'  => 'image/png',
							  'jpe'  => 'image/jpeg',
							  'jpeg' => 'image/jpeg',
							  'jpg'  => 'image/jpeg',
							  'gif'  => 'image/gif',
							  'bmp'  => 'image/bmp',
							  'ico'  => 'image/vnd.microsoft.icon',
							  'tiff' => 'image/tiff',
							  'tif'  => 'image/tiff',
							  'svg'  => 'image/svg+xml',
							  'svgz' => 'image/svg+xml'];
	private       $arch   = ['zip' => 'application/zip',
							 'rar' => 'application/x-rar-compressed',
							 'exe' => 'application/x-msdownload',
							 'msi' => 'application/x-msdownload',
							 'cab' => 'application/vnd.ms-cab-compressed'];

	public function __construct()
	{
		self::$instance = $this;
	}

	/**
	 * Возвращает ошибки файлового менеджера
	 *
	 * @return array
	 */
	public static function getErrors()
	{
		return self::$error;
	}

	/**
	 * Возвращает созданный  ранее класс.
	 *
	 * @return FS
	 */
	public static function instance()
	{
		if (!isset(self::$instance)) {
			self::$instance = new self;
		}
		return self::$instance;
	}


	/**
	 * Функция аналогична include_file, за исключением того что проводится проверка
	 * на существование файла, и в файл передаются данные из переменной $data.
	 * Путь обрабатываемого файла.
	 *
	 * @param $includeEmindTemplateFile
	 * @param array|bool $data
	 * Возвращает true если все прошло гладко, и false в случае ошибки. Все ошибки сохряняются в self::$error
	 *
	 * @return bool
	 * @internal param $file Передаваемые данные, обычно в массиве но можно в любом формате.* Передаваемые данные, обычно в массиве но можно в любом формате.
	 */
	function includeFile($includeEmindTemplateFile, $data = [])
	{
		/**
		 * Сменил префик, так как если передать массив вида['data'=>'ddd'] то переменная дата затрется, а она
		 * здесь как основная, поэтому в ее случае появится E_data в остальных случаях все как было останется
		 */
		if ($data == false) {
			$data = [];
		}
		if (is_array($data)) {
			extract($data, EXTR_PREFIX_SAME, 'E');
		}

		if (empty($includeEmindTemplateFile)) {
			self::$error[$includeEmindTemplateFile] = "НЕ ПЕРЕДАНО ИМЯ ФАЙЛА";
			return false;
		}

		if (file_exists($includeEmindTemplateFile)) {
			if (!require($includeEmindTemplateFile)) {
				self::$error[$includeEmindTemplateFile] = "НЕ УДАЛОСЬ ПОДКЛЮЧИТЬ ФАЙЛ";
				return false;
			} else {
				self::$stack [$includeEmindTemplateFile] = " - подключен файл в " . time();
				return true;
			}
		} else {
			self::$error[$includeEmindTemplateFile] = "ФАЙЛ НЕ СУЩЕСТВУЕТ";
			return false;
		}
	}

	/**
	 * Классический сеттер, который позволяет установить папку дл дальнейшей работы. С проверкой существования
	 * папки.
	 *
	 * @param $folder
	 *
	 * @return $this|bool
	 */
	function setFolder($folder)
	{
		if (empty($folder) and empty($this->folder)) {
			return false;
		} else {
			if (!empty($folder) and file_exists($folder)) {
				$this->folder = $folder;
			} else {
				self::$error[] = 'NO DIRECTORY' . $folder;
				$this->folder = null;
			}

			return $this;
		}

	}

	/**
	 * Вернет все файлы и указанной папки - только названия
	 *
	 * @param $folder
	 *
	 * @return false|mixed
	 */
	public static function files($folder)
	{
		return self::instance()->getFilesInFolder($folder);
	}

	/**
	 * Вернет перечень файлов из папки с атрибутами
	 *
	 * @param $folder
	 *
	 * @return false|mixed
	 */
	public static function fullFiles($folder)
	{
		$files = self::instance()->getFilesInFolder($folder);
		if (!$files) {
			return new \stdClass();
		}

		foreach ($files as $file) {
			$files[$file] = ['name'   => $file,
							 'size'   => filesize($folder . "/" . $file),
							 'time'   => date("d-m-Y H:i:s", filemtime($folder . "/" . $file)),
							 'rights' => substr(sprintf('%o', fileperms($folder . "/" . $file)), -4),
			];

		}
		return $files;
	}

	/**
	 * Вернет все папки из указанной, только названия
	 *
	 * @param $folder
	 *
	 * @return false|mixed
	 */
	public static function folders($folder)
	{
		return self::instance()->getFoldersInFolder($folder);
	}

	/**
	 * Вернет все папки из указанной папки, с атрибутами
	 *
	 * @param $folder
	 *
	 * @return false|mixed
	 */
	public static function fullFolders($inFolder)
	{
		$folders = self::instance()->getFoldersInFolder($inFolder);
		if (!$folders) {
			return new \stdClass();
		}
		foreach ($folders as $folder) {
			$FullPathFolder = $inFolder . "/" . $folder;
			$folders[$folder] = ['name'   => $folder,
								 'time'   => date("d-m-Y H:i:s", filemtime($FullPathFolder)),
								 'rights' => substr(sprintf('%o', fileperms($FullPathFolder)), -4),
			];
		}
		return $folders;
	}

	/**
	 * Функция возвращает списко файлов и папок аналогично scandir но из папк установеленной setFolder
	 *
	 * @return $this
	 */
	function getFolder()
	{
		$files = false;
		if (file_exists($this->folder)) {
			$files = scandir($this->folder);
		}
		if ($files) {
			$this->files = $files;
		}
		return $this;
	}

	/**
	 * @param string $folder принимает в качестве входа папку
	 * @param bool $type     и необязательный параметр, типа файла, указывается как расширение файлов. Наприме php
	 *                       на 04 05 2016 принимает тока один тип, далее будет можно передать массив.
	 */
	function getAllFiles($folder, $type = false)
	{
		if ($p = $this->getFoldersInFolder($folder)) {
			foreach ($p as $kp => $vp) {
				$this->getAllFiles($folder . "/" . $vp, $type);
			}
		}
		if ($f = $this->getFilesInFolder($folder, $type)) {
			foreach ($f as $k => $v) {
				array_push(self::$all, $folder . "/" . $v);
			}
		}
		return $this;
	}

	/**
	 * В качестве входящего параметра получает путь к папке, и возвращает список вложенных папок.
	 *
	 * @param $folder
	 *
	 * @return mixed
	 */
	function getFoldersInFolder($folder)
	{
		$new = false;
		$this->setFolder($folder)
			 ->getFolder();
		foreach ($this->files as $k => $v) {
			if (is_dir($this->folder . "/" . $v) and $v != "." and $v != "..") {
				$new[$v] = $v;
			}
		}
		//die();
		return $new;
	}

	/**
	 * Функция возвращает список файлов из папки, только файлы.
	 *
	 * @param $folder
	 *
	 * @param bool $type
	 * @param int $num -кол-во файлоа, позволит вернуть 1 файл даже :)
	 *
	 * @return mixed
	 */
	function getFilesInFolder($folder, $type = false, $num = 9999)
	{
		$new = false;
		$this->setFolder($folder)
			 ->getFolder();
		$i = 0;
		foreach ($this->files as $k => $v) {

			if (is_file($this->folder . "/" . $v) and $v != "." and $v != "..") {
				$i++;
				if ($i > $num) {
					return $new;
				}
				if ($type != false and substr($v, -3) != $type) {
					continue;
				}
				$new[$v] = $v;
			}
		}
		return $new;
	}

	/**
	 * Возвращает путь до родительской папки.
	 *
	 * @param $path
	 *
	 * @return string
	 */
	function getPareFolder($path)
	{
		$array = explode("/", $path);
		$array = array_pop($array);
		return $path = implode("/", $array);
	}

	/**
	 * Создает папку (папки) по переданному пути. В случае успеха возвращает true, если нет false
	 *
	 * @param $path
	 *
	 * @return bool
	 */
	function createFolder($path)
	{
		if (file_exists($path)) {
			return true;
		} else {
			if (mkdir($path, _FOLDER_R_, true)) {
				return true;
			} else {
				self::$error[$path] = "НЕ СМОГ СОЗДАТЬ ПАПКУ";
				return false;
			}
		}
	}

	public function getFileSizeHuman($bytes, $decimals = 2)
	{
		$size = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
		$factor = floor((strlen($bytes) - 1) / 3);
		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$size[$factor];
	}

	/**
	 * Функция удаляет папку, по переданному пути. Второй параметр, говрит удалить переданную папку, или только ее вложения.
	 *
	 * @param     $path
	 * @param int $delete
	 *
	 * @return bool
	 */
	function removeFolder($path, $delete = 1) //если 1 удаляется и сама папка!
	{
		$success = true;
		if (file_exists($path) && is_dir($path)) {
			$dirHandle = opendir($path);
			while (false !== ($file = readdir($dirHandle))) {
				if ($file != '.' && $file != '..') {
					$tmpPath = $path . '/' . $file;
					chmod($tmpPath, 0777);
					if (is_dir($tmpPath)) {
						$this->removeFolder($tmpPath);
					} else {
						if (file_exists($tmpPath)) {
							unlink($tmpPath);
						}
					}
				}
			}
			closedir($dirHandle);
			if ($delete == "1") {
				if (file_exists($path)) {
					rmdir($path);
				}
			}
		} else {
			$success = false;
		}
		return $success;
	}

	/**
	 * Проверяте что переданный путь, это файл и имеет права на чтение.
	 *
	 * @param $file
	 *
	 * @return bool
	 */
	function isFile($file)
	{
		if (file_exists($file) and is_readable($file)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Возвращает содержимое файла, с предварительной проверкой на его существование. Если же файла нет, вернут false.
	 *
	 * @param $file
	 *
	 * @return bool|string
	 */
	function readFile($file)
	{
		if ($this->isFile($file)) {
			return file_get_contents($file);
		} else {
			return false;
		}
	}

	/**
	 * Возвращает только картинки из папки по mime type
	 *
	 * @param $folder
	 *
	 * @return mixed
	 */
	public function getImagesInFolder($folder)
	{
		$files = $this->getFilesInFolder($folder);
		if (!is_array($files)) {
			return [];
		}
		foreach ($files as $k => $file) {
			if (!$this->isImg($folder . $file)) {
				unset($files[$k]);
			}
		}
		return $files;
	}

	/**
	 * Возвращает type файла в нижнем регистре
	 *
	 * @param $filename
	 *
	 * @return string
	 */
	public static function getType($filename)
	{
		return strtolower(pathinfo(basename($filename), PATHINFO_EXTENSION));

	}

	/**
	 * Принимает файл и возвращает true если это картинка и false если нет
	 * ВАЖНО: файл должен содердать полный абсолютный путь
	 *
	 * @param $file
	 *
	 * @return bool
	 */
	public function isImg($file)
	{
		$type = self::getType($file);
		return isset($this->images[$type]);
	}

	/**
	 * Принимает тип файла и возвращает, это картинка или нет ?
	 *
	 * @param $mimeType
	 *
	 * @return bool
	 */

	public function isImgByMimeType($mimeType)
	{
		return in_array($mimeType, $this->images);
	}

	/**
	 * Force Download
	 *
	 * Generates headers that force a download to happen
	 * Example usage:
	 * force_download( 'screenshot.png', './images/screenshot.png' );
	 *
	 * @access public
	 *
	 * @param string $filename
	 * @param string $data
	 *
	 * @return void
	 */
	public function force_download($filename = '', $data = '')
	{
		if ($filename == '' || $data == '') {
			return false;
		}

		if (!file_exists($data)) {
			return false;
		}
		// Try to determine if the filename includes a file extension.
		// We need it in order to set the MIME type
		if (false === strpos($filename, '.')) {
			return false;
		}

		// Grab the file extension
		$extension = self::getType($filename);

		// our list of mime types
		$mime_types = [
			'txt'  => 'text/plain',
			'htm'  => 'text/html',
			'html' => 'text/html',
			'php'  => 'text/html',
			'css'  => 'text/css',
			'js'   => 'application/javascript',
			'json' => 'application/json',
			'xml'  => 'application/xml',
			'swf'  => 'application/x-shockwave-flash',
			'flv'  => 'video/x-flv',

			// audio/video
			'mp3'  => 'audio/mpeg',
			'qt'   => 'video/quicktime',
			'mov'  => 'video/quicktime',

			// adobe
			'pdf'  => 'application/pdf',
			'psd'  => 'image/vnd.adobe.photoshop',
			'ai'   => 'application/postscript',
			'eps'  => 'application/postscript',
			'ps'   => 'application/postscript',

			// ms office
			'doc'  => 'application/msword',
			'rtf'  => 'application/rtf',
			'xls'  => 'application/vnd.ms-excel',
			'ppt'  => 'application/vnd.ms-powerpoint',

			// open office
			'odt'  => 'application/vnd.oasis.opendocument.text',
			'ods'  => 'application/vnd.oasis.opendocument.spreadsheet',
		];
		$mime_types = array_merge($mime_types, $this->arch, $this->images);
		// Set a default mime if we can't find it
		if (!isset($mime_types[$extension])) {
			$mime = 'application/octet-stream';
		} else {
			$mime = (is_array($mime_types[$extension])) ? $mime_types[$extension][0] : $mime_types[$extension];
		}
		/*
		 *  if ( $fp = fopen ($path,'r')) {
					header ( 'HTTP/1.1 200 OK');
					header ( 'Status: 200 OK' );
					header ( 'Pragma: public' );
					header ( 'Cache-Control: max-age=0' );
					header ( 'Content-length: ' . $info['Content-Length'] );
					header ( 'Content-disposition: attachment;filename='.$name );
					header ( 'Content-type: ' . $info['Content-Type'] );

					fpassthru($fp);
					fclose($fp);
					exit;
		 */
		// Generate the server headers
		if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE")) {
			header('Content-Type: "' . $mime . '"');
			header('Content-Disposition: attachment; filename="' . $filename . '"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header("Content-Transfer-Encoding: binary");
			header('Pragma: public');
			header("Content-Length: " . filesize($data));
		} else {
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private", false);
			header("Content-Type: " . $mime, true, 200);
			header('Content-Length: ' . filesize($data));
			header('Content-Disposition: attachment; filename=' . $filename);
			header("Content-Transfer-Encoding: binary");
		}
		readfile($data);
		exit;

	} //End force_download

	/**
	 * Перемещение файла...
	 *
	 * @param $from
	 * @param $to
	 *
	 * @return bool
	 */
	public function moveFile($from, $to)
	{
		if (!file_exists($from)) {
			self::$error[] = "Нет источника";
		}
		return rename($from, $to);
	}

	/**
	 * Копирование файла...
	 *
	 * @param $from
	 * @param $to
	 *
	 * @return bool
	 */
	public function copyFile($from, $to)
	{
		if (!file_exists($from)) {
			self::$error[] = "Нет источника";
		}
		return copy($from, $to);
	}

	/**
	 * Копирование папки рекурсивно
	 *
	 * @param $src
	 * @param $dst
	 */
	public function copyFolder($src, $dst)
	{
		$dir = opendir($src);
		$this->createFolder($dst);
		while (false !== ($file = readdir($dir))) {
			if (($file != '.') && ($file != '..')) {
				if (is_dir($src . '/' . $file)) {
					$this->copyFolder($src . '/' . $file, $dst . '/' . $file);
				} else {
					copy($src . '/' . $file, $dst . '/' . $file);
				}
			}
		}
		closedir($dir);
	}

	/**
	 * Перемещение загруженного файла
	 *
	 * @param $file
	 * @param $destination
	 *
	 * @return bool
	 */
	public static function moveUploaded($file, $destination)
	{
		if (file_exists($file) and is_uploaded_file($file)) {
			self::instance()->createFolder(dirname($destination));
			return move_uploaded_file($file, $destination);
		} else {
			return false;
		}

	}

}
