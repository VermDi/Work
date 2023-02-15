<section>
	<div class="panel-heading clearfix">
		<h5 class="pull-left text-uppercase">Добавление поля в AmoCRM</h5>
	</div>
	<form method="post" action="/amocrm/admin/addfield" class="form_edit_field">
		<div style="display: flex; flex-direction: column;">
			<div class="form-group">
				<label class="col-sm-2 control-label" for="title">Категория поля:</label>
				<div class="col-sm-6">
					<select name="category" class="form-control">
						<? foreach ($data['category'] as $key_type => $val_type){?>
							<option value="<?= $key_type ?>"><?= $val_type ?></option>
						<? } ?>
					</select>
					<dev><b>*</b>В какую категорию добавить поле<br></dev>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="title">Имя в AmoCRM:</label>
				<div class="col-sm-6">
					<input  type="text" name="name"
						   placeholder="Введите имя поля" class="form-control">
					<dev><b>*</b>Имя поля в Аккаунте AmoCRM<br></dev>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="title">Имя поля в form на проекте:</label>
				<div class="col-sm-6">
					<input  type="text" name="name_in_form"
						   placeholder="Введите имя поле в form" class="form-control">
					<dev><b>*</b>Имя поля в form <b>(name= "")</b>, а так же ключ в массиве для отправки данных по API<br></dev>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="title">Тип поля:</label>
				<div class="col-sm-6">
					<select name="type" class="form-control">
						<? foreach ($data['field_type'] as $key_type => $val_type){?>
							<option value="<?= $key_type ?>"><?= $val_type ?></option>
						<? } ?>
					</select>
					<dev><b>*</b>Тип тип поля для приема определенных данных<br></dev>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="title">Только из Api:</label>
				<div class="col-sm-6">
					<input  type="checkbox" name="is_api_only"><br>
					<dev><b>*</b>Редактирование поля только из API, или же из API а так же личного аккаунта AmoCRM<br></dev>
				</div>
			</div>
		</div>
		<button class="btn btn-success" type="submit">Сохранить</button>
	</form>
</section>