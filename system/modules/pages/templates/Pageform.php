<?php /*
  * @property string id -
 * @property string title  -
 * @property string meta_keywords -
 * @property string meta_description -
 * @property string meta_additional -
 * @property string content -
 * @property string design -
 * @property string menu_name -
 * @property string visible -
 * @property string url -
 * @property string create_at -
 * @property string user_id -
 * @property string level -
 * @property string left -
 * @property string right -
 * @property string domain
  *
  *
  * Visible - 0 нет, 1 - да, 2 - черновик (видит тока админ)
  *
  * */

use core\Tools;

$pid = $data[1]; //если мы создаем дочернююю страницу!
$domains = $data[2];
$data = $data[0];
function getObj($type)
{
	$obj = new \stdClass();
	$obj->type = $type;
	$obj->id = Tools::passFromString(4, 6);
	$obj->isActive = false;
	$obj->data = [];
	return $obj;

}
if (strpos(substr($data->content,0,5), "[")===false){
	$arr = [];
	$row = getObj("row");
	$block = getObj('block');
	$codeBlock = getObj("wysiwyg");
	$codeBlock->data = $data->content==null?[]:$data->content;
	$block->data[] = $codeBlock;
	$row->data[] = $block;
	$arr[]=$row;
	$data->content = json_encode($arr);
}
if (strpos($data->content, "\"content\":") !== false) {

	$arr = [];
	$temp = json_decode($data->content);
	foreach ($temp as $item){
		if($item->type=="code"){
			$row = getObj("row");
			$block = getObj('block');
			$codeBlock = getObj("code");
			$codeBlock->data = htmlspecialchars_decode($item->content);
			$block->data[] = $codeBlock;
			$row->data[] = $block;
			$arr[]=$row;
		}
		if($item->type=="wysiwyg"){
			$row = getObj("row");
			$block = getObj('block');
			$codeBlock = getObj("wysiwyg");
			$codeBlock->data = $item->content;
			$block->data[] = $codeBlock;
			$row->data[] = $block;
			$arr[]=$row;
		}
	}
	$data->content = json_encode($arr);
}
?>

<div id="pagesFormRow" class="panel row" style="box-shadow: -7px 0px 4px -4px rgba(0, 0, 0, .2);">
	<form action="/pages/admin/save" method="post" class="form-horizontal form" enctype="multipart/form-data"
		  id="PageForm">
		<div class="panel-heading clearfix">
			<div class="pull-left closeOnCollapse">Форма редактирования
				страницы <?php if ($domains !== false and is_array($domains)){ ?>
				для домена:
			</div>
			<div class="pull-right">
				<select name="domain" class="form-control"> <?
					foreach ($domains as $k => $v) {
						?>
						<option value="<?= $v; ?>"><?= $v; ?></option>
					<?php }
					?>
				</select>
			</div><? } ?>
		</div>
		<div class="pull-right">
			<span class="btn btn-default" id="collapseForm">&raquo;</span>
		</div>
</div>
<div class="panel-body pages-form closeOnCollapse">
	<?php if ($data->id > 0) { ?><input type="hidden" name="id" value="<?= $data->id; ?>"> <?php } ?>
	<?php if ($pid != false) { ?><input type="hidden" name="pid" value="<?= $pid; ?>"> <?php } ?>
	<div class="row clearfix">
		<div class="col-sm-6">
			<div class="input-group-sm">
				<label for="url">URL страницы</label>
				<input type="text" name="url" id="url" class="form-control" value="<?= $data->url; ?>">
				<span class="help-inline" id="numtypes">0</span>
			</div>

			<label for="title">Заголовок страницы Title</label>
			<input type="text" name="title" id="title" class="form-control" required
				   value="<?= $data->title; ?>">
			<span class="help-inline" id="numtypestitle">0</span>

			<label for="h1_text">Заголовок для H1</label>
			<input type="text" name="h1_text" id="h1_text" class="form-control"
				   value="<?= $data->h1_text; ?>">

		</div>
		<div class="col-sm-6">
			<label for="menu_name">Название для меню</label>
			<input type="text" name="menu_name" class="form-control" value="<?= $data->menu_name; ?>">
			<div class="col-sm-6" style="padding: 0px; padding-right: 7px;">
				<label for="design">Дизайн</label>
				<select name="design" class="form-control" for="design">    <?
					$list = \core\FS::instance()->getFoldersInFolder(_SYS_PATH_ . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "front");
					foreach ($list as $v) {
						?>
						<option
							value="<?= $v; ?>" <?= ($v == $data->design) ? "selected" : ""; ?>><?= $v; ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="col-sm-6" style="padding: 0px; padding-left: 7px;">
				<label for="visible">Видимость</label>
				<select name="visible" class="form-control">
					<option value="1" <?= ($data->visible == 1 or empty($data->visible)) ? "selected" : ""; ?>>
						Видима
					</option>
					<option value="0" <?= ($data->visible == 0) ? "selected" : ""; ?>>Скрыта</option>
					<option value="2" <?= ($data->visible == 2) ? "selected" : ""; ?>>Черновик</option>
				</select>
			</div>
			<label for="bg_img" style="width: 100%;">Фоновая картинка
				<? if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/public/images/pages/" . md5($data->id) . "/bg.jpg")) { ?>
				<a href="/pages/admin/deleteimg/<?= $data->id; ?>" class="pull-right">Удалить
						картинку</a><? } ?></label>
			<input type="file" name="bg_img" class="form-control">
		</div>

	</div>
	<div class="row form-group-sm clearfix"
		 style="margin-top: 25px;background-color: #f7f7f7;padding: 15px 0px;">
		<div class="col-sm-6">
			<label for="meta_keywords">Meta keywords</label>
			<input type="text" name="meta_keywords" class="form-control"
				   value="<?= $data->meta_keywords; ?>">
			<label for="meta_description">Meta description</label>
			<textarea type="text" name="meta_description"
					  class="form-control"><?= $data->meta_description; ?></textarea>
		</div>
		<div class="col-sm-6">
			<label for="meta_additional">Кастомные meta поля</label>
			<textarea type="text" name="meta_additional" class="form-control"
					  rows="5"><?= $data->meta_additional; ?></textarea>
		</div>
	</div>
	<div class="row form-group-sm clearfix">
		<div class="panel" id="ContentZoneInForm">
		</div>
	</div>
	<div class="col-sm-12 row"
		 style="position: fixed; background-color: #f1f2f7; bottom: 24px; left: 210px;padding: 5px 29px;margin: 0px; width:calc(100% - 210px)">
		<button class='btn btn-success col-sm-5' id='sendAndOut' type='submit'>Сохранить</button>
		<button class='btn btn-warning col-sm-5 col-sm-offset-2' id='sendAndStop' type='submit'>Сохранить и
			остаться
		</button>

	</div>

</div>
</form>
</div>
<link rel='stylesheet' href='/assets/modules/pages/js/builder.css?v=150220231605'>
<script defer src='/assets/modules/pages/js/builder.js?v=150220231605'></script>
<style>

	.dinamic-block {
		width: 100%;
		border: 1px solid #f1f2f7;
	}

	.dinamic-block-head {
		width: 100%;
		background-color: #f1f2f7;
		padding: 3px 15px;
	}

	.dinamic-block-head i {
		display: block;
		float: right;
		cursor: pointer;
	}

	.dinamic-block-content {
		width: 100%;
		padding: 3px 15px;
	}

	#ContentZone {
		margin: 15px 0px;
		/*border: 1px solid #f1f2f7;*/
		/*box-shadow: 0 0 7px rgba(0, 0, 0, 0.2); !* Параметры тени *!*/
	}

	.pages-form label {
		font-size: 13px;
		font-weight: normal;
	}

	.help-inline {
		font-size: 10px;
		float: right;
	}

	.block-ckeditor, .block-text, .block-widget {
		border: 1px solid #f1f2f7;
		margin: 15px 0px;
	}

	#contentZone {
		float: right;
		transition-duration: 1s;
	}

	.smallCollapse {
		width: 80px;
		float: right;
	}

	.smallCollapse .closeOnCollapse {
		display: none;
	}

</style>
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", GO);
	document.getElementById('collapseForm').onclick = function () {
		document.getElementById('contentZone').classList.toggle('smallCollapse');
		if (this.innerHTML == "»") {
			this.innerHTML = "«";
		} else {
			this.innerHTML = "»";
		}
	};


	function GO() {

		let builder = EMB(document.getElementById('ContentZoneInForm'));
		<?php if(!empty($data->content)){
		?>
			builder.setData(<?=$data->content;?>);
		<?
		}
		?>
		setTimeout(function () {


			if (CKEDITOR) {
				let fields = document.querySelectorAll("div[contenteditable]");
				let conf = {
					disableNativeSpellChecker: false,// отключает запрет на стандартную проверку орфографии браузером
					extraPlugins: 'sourcedialog',
					removeButtons: 'source, Print,Form,TextField,Textarea,radio, checkbox,imagebutton,Button,SelectAll,Select,HiddenField',
					removePlugins: 'source, spellchecker, Form, TextField,Textarea, Button, Select, HiddenField , about, save, newpage, print,exportpdf, templates, scayt, flash, pagebreak, smiley,preview,find',
					filebrowserBrowseUrl: '/assets/vendors/filemanager/dialog.php?type=2&editor=ckeditor&fldr=',
					filebrowserUploadUrl: '/assets/vendors/filemanager/dialog.php?type=2&editor=ckeditor&fldr=',
					filebrowserImageBrowseUrl: '/assets/vendors/filemanager/dialog.php?type=1&editor=ckeditor&fldr=',
				};
				for (const el of fields) {

					CKEDITOR.inline(el, conf);
				}
			} else {
				console.log('noEditor');
			}
		}, 1000);
		/*
		 * ссылка
		 */
		var url = document.getElementById('url');
		var counter = document.getElementById('numtypes');
		counter.innerHTML = url.value.length;

		function showCount() {
			counter.innerHTML = url.value.length;
		}

		url.onChange = url.onkeydown = url.onblur = url.onfocus = showCount;
		/*
		 * Заголовок
		 */
		var title = document.getElementById('title');
		var tcounter = document.getElementById('numtypestitle');
		tcounter.innerHTML = title.value.length;

		function showTitleCount() {
			tcounter.innerHTML = title.value.length;
		}


		title.onChange = title.onkeydown = title.onblur = title.onfocus = showTitleCount;
		/*
		 * Отправка формы на сервер ajax! Она уже пашет не как ajax а это добавляет аякс
		 */
		$("#PageForm").submit(function (e) {

			var btn = $(document.activeElement, this).attr('id');
			e.preventDefault();
			let d = new FormData(this);
			var href = $(this).attr('action');
			//var d = $(this).serialize();
			d.append('content', JSON.stringify(builder.getData()));
			//console.log(d);
			/**  @#$%^&*()_!@#$%^&*()_+%$^%&*   */

			$.ajax({
				type: "POST",
				url: href,
				data: d,
				processData: false,
				contentType: false,
				success: function (msg) {
					var result = JSON.parse(msg);
					if (result.error == 0) {
						if (btn == 'sendAndOut') {
						}
						if (btn == 'sendAndStop') {
							$("#PageForm").append('<input type="hidden" name="id" value="' + result.data.id + '">');
							$('#url').val(result.data.url);
							$.toast({
								heading: 'Успешно!',
								text: 'Внесенные изменения сохранены!',
								position: 'top-right',
								icon: 'success'
							});

						}
						listing.jstree('refresh');
					} else {
						$.toast({
							heading: 'ОШИБКА!',
							text: 'Что то пошло не так!',
							position: 'top-right',
							icon: 'danger'
						});
					}
				}
			});
			return false;
		});

	};


</script>

