<?php
include_once __DIR__ . '/../templates/menu.php';
?>

<section>
	<div class="panel-heading clearfix">
		<h5 class="pull-left text-uppercase">Настройка CRM</h5>
		<a href="/amocrm/amo/token" class="btn btn-success pull-right"><i class="glyphicon glyphicon-plus"></i>Получить
			первичный
			токен</a>
	</div>
	<div class="amo_img_box">
		<div class="image">
			<img width="30%" src="/assets/modules/amocrm/img/Amo_settings1.png">
		</div>
		<div class="image">
			<img width="30%" src="/assets/modules/amocrm/img/Amo_settings2.png">
		</div>
		<div class="image">
			<img width="30%" src="/assets/modules/amocrm/img/Amo_settings3.png">
		</div>
	</div>
	<div style="margin-bottom: 20px">
		<p>
		<h6>Для того чтобы подключить ваш проект к amoCRM нужно сделать следующие действия:</h6>
		<b>1)</b> Создать аккаунт на amoCRM.<br>
		<b>2)</b> После этого переходим в amoМаркет и создаем новую интеграцию.<br>
		<b>3)</b> Во время создания интеграции вам нужно обязательно указать адрес вашего сайта
		<b>(https://xxxxxx.xxx/amocrm/amo/token)</b>, предоставить все доступы для данной
		интеграции, заполнить оставшиеся поля и после чего сохранить.<br>
		<b>*</b> Введенный адрес сайта <b>дублируем</b> в форму ниже - поле «Redirect URL».<br>
		<b>4)</b> После создания интеграции, переходим во вкладку «Установленные». Нажимаем на созданную интеграцию и
		дальше
		переходим во вкладку «Ключи и доступы» — эти данные нам понадобятся для
		авторизации нашей интеграции. Мы не будем их использовать при каждом запросе, они нужным нам для получения
		Первичного токена для работы нашей интеграции на проекте.<br>
		<b>*</b> ID интеграции <b>дублируем</b> в форму ниже - поле «ID Интеграции».<br>
		<b>*</b> Секретный ключ <b>дублируем</b> в форму ниже - поле «Секретный ключ».<br>
		<b>5)</b> После заполнения всех полей в форме ниже, нажимаем сперва «Сохранить». После сохранения нажимаем на
		«Получить первичный токен»<br>
		<b>6)</b> После получения первичного токенна, больше нам что либо вводить на этой странице не нужно, <b>Интеграция
			подключена к вашему проекту</b><br>
		</p>
	</div>
	<form method="post" class="form-amocrm">
		<div style="display: flex; flex-direction: column;">
			<div class="form-group">
                <input value="<?= isset($data->id) ? $data->id : '' ?>" type="hidden" name="id">
				<label class="col-sm-2 control-label" for="title">ID Интеграции:</label>
				<div class="col-sm-7">
					<input value="<?= isset($data->client_id) ? $data->client_id : '' ?>" type="text"
						   name="client_id"
						   placeholder="Введите ID Интеграции" class="form-control">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="title">Секретный ключ:</label>
				<div class="col-sm-7">
					<input value="<?= isset($data->client_secret) ? $data->client_secret : '' ?>" type="text"
						   name="client_secret"
						   placeholder="Введите Секретный ключ" class="form-control">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="title">Redirect URL:</label>
				<div class="col-sm-7">
					<input value="<?= isset($data->redirect_url) ? $data->redirect_url : '' ?>" type="text"
						   name="redirect_url"
						   placeholder="Введите Redirect URL" class="form-control">
				</div>
			</div>
		</div>
		<button class="btn btn-success" type="submit">Сохранить</button>
	</form>
</section>
