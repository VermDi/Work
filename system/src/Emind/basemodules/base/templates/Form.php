<?php
/**
 * Created by PhpStorm.
 * User: Евгений
 * Date: 14.11.2017
 * Time: 18:54
 */
// Данные для копипаста... Модель стандартная!!!!
?>
<div class="panel">
	<div class="panel-heading">Добавить Тип
		<div class="pull-right"><a href="/<?= \core\App::$module; ?>/admin" class="btn btn-success btn-xs">К списку</a>
		</div>
	</div>
	<div class="panel-body">
		<form action="/<?= \core\App::$module; ?>/<?= \core\App::$controller ?>/save" method="post"
			  class="form-horizontal"
			  enctype="multipart/form-data">
			<label for="name">Название</label>
			<input type='text' name='name' class="form-control emind-counter" id="primary"
				   value="<?= $data['data']->name; ?>" required>
			<?php if ($data['data']->id) { ?>
				<input type='hidden' name='id' class="form-control" value="<?= $data['data']->id; ?>">
			<?php } ?>
			<label for="name">Заголовок</label>
			<input type='text' name='title' class="form-control" value="<?= $data['data']->title; ?>">
			<label for="description">Описание</label>
			<textarea name='description' class="form-control emind-ckeditor"><?= $data['data']->content; ?></textarea>
			<div class="row">
				<div class="col-sm-12">
					<label for="meta_keywords">Мета ключевые слова </label>
					<input type='text' name='meta_keywords' class="form-control"
						   value="<?= $data['data']->meta_keywords; ?>">
				</div>
				<div class="clear">
					<div class="col-sm-6">
						<label for="meta_description">Мета описание</label>
						<textarea name='meta_description'
								  class="form-control"><?= $data['data']->meta_description; ?></textarea>
					</div>
					<div class="col-sm-6">
						<label for="meta_additional">Ваши мета</label>
						<textarea name='meta_additional'
								  class="form-control"><?= $data['data']->meta_additional; ?></textarea>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<label for="visible">Видимость</label>
					<p>
						<input type='radio' name='visible' value="1" <?php if ($data['data']->visible!='0') {
							echo "checked";
						} ?>> - да
						<input type='radio' name='visible' value="0" <?php if ($data['data']->visible=='0') {
							echo "checked";
						} ?>> - нет
					</p>
				</div>
			</div>
			<div class="col-sm-12">
				<?php if ($data['data']->id) {
					if (file_exists($_SERVER['DOCUMENT_ROOT']."/public/phones/type/".$data['data']->id.".png")) {
						?>
						<div class="col-sm-2"> <img src="<?= "/public/phones/type/".$data['data']->id.".png"; ?>"
													class="pull-left"
													style="margin: 7px;"><br>
						<input type="checkbox" name="delete_picture" value="1"> - удалить фото </div><?
					}
				} ?>
				<div class="col-sm-6"><label for="">Выберите изображение</label>
					<?= \SimpleForm\SimpleForm::Img('ico')
											  ->setId('ico')
											  ->setClass(['form-control-file'])
											  ->show(); ?>
				</div>

			</div>
			<button class='btn btn-success' id='sendAndStop' type='submit' style="margin-top: 15px;">Сохранить</button>
		</form>
	</div>
</div>


<script src="/assets/vendors/e-mindhelpers/Formhelper.js" type="text/javascript" defer></script>

