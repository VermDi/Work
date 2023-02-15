<div class="panel">
	<div class="panel-heading">
		<a href="/menu/admin" class="btn btn-xs btn-default">&larr; Вернуться к меню </a> Настройте параметры для меню
		<a href="/menu/admin/rebuild" class="pull-right btn btn-xs btn-warning">Ребилд всех позиций</a>
	</div>
	<div class="panel-body">
		<blockquote>Определитесь с теми параметрами, что еще понадобятся для меню, и внесите их в правое поле.
		</blockquote>
		<div class="row">
			<div class="col-sm-3">
				<div id="input-field">
					<div class="param btn btn-xs btn-default" style="width: 100%; margin-top: 15px;"
						 onclick="addField(this);" id="input-button">
						Input поле, для текста &rarr;
					</div>
					<div class="content hidden">
						<div class="zona" data-type="input"><span class="pull-right" onclick="del(this);"><i
									class="fa fa-close"></i> </span> <label>[INPUT] Название поля</label>
							<input type="text" name="field-title" class="form-control"
								   onkeyup="changeField(this);">
							<label>Название параметра (техническое)</label>
							<input type="text" name="field-name" class="form-control">
						</div>
					</div>
					<div class="view hidden">
						<div>
							<label for="">
							</label>
							<input type="text" name="" class="form-control">
						</div>
					</div>
				</div>
				<div id="textarea-field">
					<div class="param btn btn-xs btn-default" id="textarea-button"
						 style="width: 100%; margin-top: 15px;"
						 onclick="addField(this);">
						Textarea поле, для текста &rarr;
					</div>
					<div class="content hidden">
						<div class="zona" data-type="textarea">
							<span class="pull-right" onclick="del(this);"><i class="fa fa-close"></i> </span> <label>[TEXTAREA]
								Название поля</label>
							<input type="text" name="field-title" class="form-control"
								   onkeyup="changeField(this);">
							<label>Название параметра (техническое)</label>
							<input type="text" name="field-name" class="form-control">
						</div>
					</div>
					<div class="view hidden">
						<div>
							<label for="">
							</label>
							<textarea name="" class="form-control">

							</textarea>
						</div>
					</div>
				</div>
				<div id="checkbox-field">
					<div class="param btn btn-xs btn-default" style="width: 100%; margin-top: 15px;"
						 onclick="addField(this);" id="checkbox-button">
						Checkbox, для маркера &rarr;
					</div>
					<div class="content hidden">
						<div class="zona" data-type="checkbox">
							<span class="pull-right" onclick="del(this);"><i class="fa fa-close"></i> </span> <label>[CHECKBOX]
								Название поля</label>
							<input type="text" name="field-title" class="form-control"
								   onkeyup="changeField(this);">
							<label>Название параметра (техническое)</label>
							<input type="text" name="field-name" class="form-control">
							<label>Значение для отмеченного поля</label>
							<input type="text" name="field-value" class="form-control">
						</div>
					</div>
					<div class="view hidden">
						<div>
							<label for="">
							</label>
							<input type="checkbox" name="" value="">
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-5">
				<form method="post" action="/menu/admin/saveparameters">
					<div id="form-content">

					</div>
					<button type="submit" id="submit-button" class="btn btn-success hidden" style="width: 100%;"
							onclick="return send();">
						Сохранить изменения
					</button>
					<span class="alarm-text">* если вы изменили набор полей, то для всех пунктов меню его придется пересоздать. Старые совпадающие значения по имени сохранятся.</span>
				</form>
			</div>
			<div class="col-sm-4" id="view-zone">

			</div>
		</div>

	</div>
</div>
<script>
	let zone = document.getElementById('form-content');
	let viewZone = document.getElementById('view-zone');

	function randTxt(length) {
		var result = '';
		var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		var charactersLength = characters.length;
		for (var i = 0; i < length; i++) {
			result += characters.charAt(Math.floor(Math.random() * charactersLength));
		}
		return result;
	}

	function addField(obj, item = null) {

		let randomCode = randTxt(7);
		let field = obj.parentNode.querySelector(".content");
		let viewField = obj.parentNode.querySelector(".view");
		if (field) {
			field.querySelector('div').setAttribute('data-id', randomCode);
			viewField.querySelector('div').setAttribute('data-id', randomCode);
			zone.insertAdjacentHTML('beforeend', field.innerHTML);
			if (viewField) {
				viewZone.insertAdjacentHTML('beforeend', viewField.innerHTML);
			}
			if (typeof item != null && item != null) {
				zone.querySelector("div[data-id='" + randomCode + "']").querySelector("input[name=field-title]").value = item.title;
				zone.querySelector("div[data-id='" + randomCode + "']").querySelector("input[name=field-name]").value = item.name;
				let lf = zone.querySelector("div[data-id='" + randomCode + "']").querySelector("input[name=field-value]");
				if (lf) {
					lf.value = item.value;
				}
				viewZone.querySelector("div[data-id='" + randomCode + "']").querySelector('label').innerHTML = item.title;
			}
		}

		document.getElementById('submit-button').classList.remove('hidden');
	}

	function changeField(obj) {
		let code = obj.parentNode.getAttribute('data-id');
		let field = viewZone.querySelector("div[data-id='" + code + "']").querySelector("label").innerHTML = obj.value;
	}

	let finalData = [];

	function send() {

		let i = 0;
		zone.querySelectorAll('.zona').forEach(function (item) {
			finalData[i] = {};
			finalData[i].name = item.querySelector('input[name=field-name]').value;
			finalData[i].title = item.querySelector('input[name=field-title]').value;
			finalData[i].type = item.getAttribute('data-type');
			let val = item.querySelector('input[name=field-value]');
			if (val) {
				finalData[i].value = item.querySelector('input[name=field-value]').value;
			} else {
				finalData[i].value = "";
			}
			i++;
		});
		//console.log(finalData, JSON.stringify(finalData));
		let dt = new FormData();
		dt.append('data', JSON.stringify(finalData));
		$EM.ajax.post('/menu/admin/saveparameters', dt).then(function (response) {
			let dt = JSON.parse(response);
			if (typeof dt.data != "undefined" && dt.data == 'ok') {
				$EM.html.toast.show({position: 'top-right', text: 'Все сохранил успешно!'});
			}
		});
		return false;

	}

	function del(obj) {
		let code = obj.parentNode.getAttribute('data-id');
		obj.parentNode.remove();
		viewZone.querySelector("div[data-id='" + code + "']").remove();
	}

	document.addEventListener("DOMContentLoaded", function () {
		let initial = <?=json_encode($data);?>;
		if (Array.isArray(initial)) {
			initial.forEach(function (item) {
				let btn = document.getElementById(item.type + '-button');
				addField(btn, item);
			});
		}
	});
</script>
<style>
	.alarm-text {
		background-color: #E5EABF;
		padding: 1px 15px;
		width: 100%;
		display: block;
		margin-top: 15px;
	}

	label {
		font-size: 11px;
		background-color: cornsilk;
		padding: 3px 11px;
		margin-bottom: 0px
	}

	.zona {
		padding: 7px 18px 16px 18px;
		outline: 1px solid #f1f2f7;
		border-radius: 3px;
		margin-bottom: 7px;
		background-color: antiquewhite;
	}

	#submit-button {
		width: 100%;
		position: fixed;
		bottom: 25px;
		left: 0px;
	}

	span.pull-right {
		cursor: pointer;
	}
</style>
