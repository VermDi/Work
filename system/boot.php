<?php
/*
* ВНИМАНИЕ ДАННАЯ КОНСТУКЦИЯ РАБОТАЕТ ПО НЕЙМСПЭЙСУ!!!
*/
set_include_path(get_include_path()
                 . PATH_SEPARATOR . __DIR__ . DIRECTORY_SEPARATOR . 'src'
                 . PATH_SEPARATOR . __DIR__
);
spl_autoload_register(function ($class_name) {
    $file = stream_resolve_include_path(str_replace("\\", DIRECTORY_SEPARATOR, $class_name) . ".php");
    file_exists($file) && require_once $file;
});

if (file_exists(__DIR__.DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."vendor".DIRECTORY_SEPARATOR."autoload.php")){

	include_once __DIR__.DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."vendor".DIRECTORY_SEPARATOR."autoload.php";
}
session_start();