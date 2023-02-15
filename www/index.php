<?php
/**
 * Created by PhpStorm.
 * User: E_dulentsov
 * Date: 18.05.2017
 * Time: 8:38
 */


include_once(__DIR__ . "/../system/core/StaticCache.php");
$cf = (new core\StaticCache())->fullCacheFilePath;
if (is_readable($cf)) {
	//добавить корс заголовки если нужны
//	header('Access-Control-Allow-Origin' ,'*');
//	header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
//	header('Access-Control-Allow-Headers', '*');
//	header("Content-type: application/json; charset=utf-8");
	if (isset($_GET['clear_cache'])) {
		unlink($cf);
	} else {
		$content = file_get_contents($cf);
		if ($content) {
			echo $content . "<!-- SHOW FROM STATIC CACHE -->";
			die();
		}
	}
}

date_default_timezone_set('Etc/GMT+3');
if (!isset($_SERVER['DOCUMENT_ROOT'])) {
	$_SERVER['DOCUMENT_ROOT'] = __DIR__;
}
function pp($data){
	echo "<pre>";
		print_r($data);
	echo "</pre>";

}
function dd($data){
	pp($data);
	die();

}
include_once(__DIR__ . "/../system/boot.php");
register_shutdown_function(function () {
	$e = error_get_last();
	if (is_array($e) and isset($e['type'])) {
		$err = \modules\admin\models\ErrorsLog::instance();
		$err->error = " IN FILE:" . $e['file'] . "\r\n LINE: " . $e['line'] . "\r\n TEXT: " . $e['message'];
		$err->date_time = date("Y-m-d H:i:s", time());
		$err->save();
	}
});

//\core\helpers\Headers::send();
function html()
{
	return \core\Html::instance();
}

\core\App::instance();

/**
 * Финальная очистка из Symfony
 */
$targetLevel = 0;
$flush = true;
$status = ob_get_status(true);
$level = count($status);
// PHP_OUTPUT_HANDLER_* are not defined on HHVM 3.3
$flags = defined('PHP_OUTPUT_HANDLER_REMOVABLE') ? PHP_OUTPUT_HANDLER_REMOVABLE | ($flush ? PHP_OUTPUT_HANDLER_FLUSHABLE : PHP_OUTPUT_HANDLER_CLEANABLE) : -1;
while ($level-- > $targetLevel && ($s = $status[$level]) && (!isset($s['del']) ? !isset($s['flags']) || ($s['flags'] & $flags) === $flags : $s['del'])) {
	if ($flush) {
		ob_end_flush();
	} else {
		ob_end_clean();
	}
}