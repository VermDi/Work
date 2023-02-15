<?php
/**
 * Created by PhpStorm.
 * User: E_dulentsov
 * Date: 09.06.2017
 * Time: 17:01
 */
?>
<div class="panel row">
	<div class="panel-heading clearfix"
		 style="position: -webkit-sticky;position: -moz-sticky;position: -ms-sticky;position: -o-sticky;position: sticky; top: 56px; background-color: white; z-index: 9;">
		<div class="pull-left">Страницы сайта</div>
		<div class="pull-right"><a href="/pages/admin/form/0/ajax" class="btn btn-sm btn-success ajax">Создать
				страницу</a>
			<a href="/pages/admin/rebuild" class="btn btn-xs btn-warning ajax">Сбросить сортировку
			</a></div>
	</div>
	<?php if ($data['domains'] !== false and is_array($data['domains'])) {
		?>
		<div class="panel-heading clearfix"><span class="pull-left">Домены:  </span>
			<?php
			foreach ($data['domains'] as $k => $v) { ?>
				<a href="/pages/admin?domain=<?= $v->domain; ?>"
				   class="btn btn-xs <?= (isset($_GET['domain']) and addslashes($_GET['domain']) == $v->domain) ? "btn-primary" : "btn-default"; ?> pull-left">
					<?= $v->domain; ?>
				</a>
			<?php } ?>
		</div>
	<?php } ?>

	<div class="panel-body">
		<div class="col-sm-3">
			<div class="clear clearfix">Поиск<br>
				<input type="text" name="finded" id="finded">
			</div>
			<div class="clear clearfix" id="list_pages">
				<? /*print_r(\modules\pages\helpers\Nested::printNestedTree($data['data'], 0, false)); die('www'); */ ?>
			</div>
		</div>
		<div class="col-sm-9" id="contentZone"
			 style="padding-bottom: 50px; background-color:white; outline:1px solid #eff2f7; position: -webkit-sticky;position: -moz-sticky;position: -ms-sticky;position: -o-sticky;top: 103px; overflow-y: scroll; margin-top: -15px; height:calc( 100vh - 170px );position: sticky;"></div>
	</div>
</div>
<?

\core\Html::instance()->setCss("/assets/vendors/jstree/themes/default/style.css");
\core\Html::instance()->setJs("/assets/vendors/jstree/jstree.min.js");
\core\Html::instance()->setJs("/assets/modules/pages/js/list.js");
\core\Html::instance()->setJs("/assets/vendors/ckeditor/ckeditor.js");
\core\Html::instance()->setJs("/assets/vendors/ckeditor/adapters/jquery.js");
\core\Html::instance()->setCss("/assets/vendors/toastr/dist/jquery.toast.min.css");
\core\Html::instance()->setJs("/assets/vendors/toastr/dist/jquery.toast.min.js");
\core\Html::instance()->setJs("/assets/vendors/e-mindhelpers/ContentFormBuilder.js");
?>
