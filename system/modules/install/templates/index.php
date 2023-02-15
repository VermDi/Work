<?php
/**
 * Created by PhpStorm.
 * User: Евгения
 * Date: 12.04.2018
 * Time: 13:54
 */
?>

<div class="row">
	<div class="wrapper">
		<h3>Установка Mind CMS</h3><br>
		<div class="head_title">Укажите настройки подключения к БД</div>
		<br>
		<form class="" role="form" id="installation_form">
			<div class="form-group">
				<label for="db_server">Сервер БД</label>
				<input type="text" id="db_server" value="" name="db_server" class="form-control"
					   placeholder="localhost или укажите свой">
			</div>
			<div class="form-group">
				<label for="db_name">Имя БД </label>
				<input type="text" id="db_name" value="" name="db_name" class="form-control">
			</div>
			<div class="form-group">
				<label for="db_port">Порт БД </label>
				<input type="text" id="db_port" value="" name="db_port" class="form-control" placeholder="По умолчанию: 3306">

			</div>
			<div class="form-group">
				<label for="db_username">Имя пользователя БД</label>
				<input type="text" id="db_username" value="" name="db_username" class="form-control">
			</div>
			<div class="form-group">
				<label for="db_pass">Пароль пользователя БД</label>
				<input type="password" id="db_pass" value="" name="db_pass" class="form-control">
			</div>
			<p>Ваш логин и пароль после установки:</p>
			<p>Логин: admin</p>
			<p>Пароль: 123
				<span style="color: red;">(Не забудьте сменить его, сразу после установки)</span></p>
			<input type="button" value="Отправить" id="start_installation" class="btn btn-success">
		</form>
	</div>
</div>
<div class=""></div>
<div class="result" style="font-size: 16px; color: red;"></div>