<?php
$url = $_SERVER['REQUEST_URI'];
$url = explode('?', $url);
$url = $url[0];

$url_settings = '/tinkoff/admin/settings';
$url_test = '/tinkoff/admin/test';
$url_manual = '/tinkoff/admin';

?>
<div class="btn-group" style="width:100%; padding: 5px 0; background: white;" id="menu_marketing">
    <a style="margin: 5px; <?= $url == $url_manual ? 'background-color: #48a5c8;' : '' ?>"
       href="<?= $url_manual ?>" class="btn btn-info">Инструкция по выводу кнопки на проект</a>
	<a style="margin: 5px; <?= $url == $url_settings ? 'background-color: #48a5c8;' : '' ?>"
	   href="<?= $url_settings ?>" class="btn btn-info">Настройка Кредитования Tinkoff</a>
</div>
