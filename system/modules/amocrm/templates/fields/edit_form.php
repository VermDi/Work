
<section>
	<div class="panel-heading clearfix">
		<h5 class="pull-left text-uppercase">Редактирование поля CRM</h5>
	</div>
	<form method="post" action="/amocrm/admin/edit/<?= $data['field']['id'] ?>" class="form_edit_field">
		<div style="display: flex; flex-direction: column;">
			<input type="hidden" value="<?= isset($data['lead']['id']) ? $data['lead']['id'] : '' ?>">
			<input type="hidden" value="<?= isset($data['field']['id']) ? $data['field']['id'] : '' ?>" name="field_id">
			<div class="form-group">
				<label class="col-sm-2 control-label" for="title">Имя в AmoCRM:</label>
				<div class="col-sm-6">
					<input value="<?= isset($data['lead']['name']) ? $data['lead']['name'] : '' ?>" type="text"
						   name="name"
						   placeholder="Введите имя поля" class="form-control">
					<dev><b>*</b>Имя поля в Аккаунте AmoCRM<br></dev>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="title">Имя поля в form на проекте:</label>
				<div class="col-sm-6">
					<input value="<?= isset($data['field']['name_in_form']) ? $data['field']['name_in_form'] : '' ?>"
						   type="text" name="name_in_form"
						   placeholder="Введите имя поле в form" class="form-control">
					<dev><b>*</b>Имя поля в form <b>(name= "")</b>, а так же ключ в массиве для отправки данных по API<br></dev>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="title">Тип поля:</label>
				<div class="col-sm-6">
					<select name="type" class="form-control">
						<? foreach ($data['field_type'] as $key_type => $val_type) {
							$selected = '';
							if ($key_type == $data['lead']['type']) {
								$selected = 'selected';
							} ?>
							<option <?= $selected ?> value="<?= $key_type ?>"><?= $val_type ?></option>
						<? } ?>
					</select>
					<dev><b>*</b>Тип тип поля для приема определенных данных<br></dev>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="title">Только из Api:</label>
				<div class="col-sm-6">
					<input <?= $data['lead']['is_api_only'] == 1 ? 'checked' : '' ?> type="checkbox"
																					 name="is_api_only"><br>
					<dev><b>*</b>Редактирование поля только из API, или же из API так же личного аккаунта AmoCRM<br></dev>
				</div>
			</div>
		</div>
		<button class="btn btn-success" type="submit">Сохранить</button>
		<button class="btn btn-success" id="sendAndStop" type="submit" name="sendAndStop_no">Сохранить и
			остаться
		</button>
	</form>
</section>