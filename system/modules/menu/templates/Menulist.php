<?php
/**
 * Created by PhpStorm.
 * User: E_dulentsov
 * Date: 09.06.2017
 * Time: 17:01
 */
?>
<div class="panel row">
	<div class="panel-heading clearfix">
		<div class="pull-left">Меню сайта</div>
		<div class="pull-right">
			<a href="/menu/admin/form/0/ajax" class="btn btn-sm btn-success ajax">
				<i class="fa fa-plus"></i> Создать пункт меню</a>
			<a href="/menu/admin/parameters" class="btn  btn-sm btn-warning"><i class="fa fa-cogs"></i> Настройки</a></div>
	</div>
	<div class="panel-body">
		<div class="col-sm-3">
			<div class="clear clearfix">Поиск<br>
				<input type="text" name="finded" id="finded">
			</div>
			<div class="clear clearfix" id="list_pages">
				<?php \modules\menu\helpers\Nested::printNestedTree($data, 0, false); ?>
			</div>
		</div>
		<div class="col-sm-9" id="contentZone"></div>
	</div>
</div>
<?php /*
<div class="modal fade" id="Modalka" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content-wrap">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">Страницы</h4>
                </div>
                <div class="modal-body" id="Modalkabody"> .
                </div>
            </div>

        </div>
    </div>
</div>
*/ ?>

<?
\core\Html::instance()->setCss("/assets/vendors/jstree/themes/default/style.css");
\core\Html::instance()->setJs("/assets/vendors/jstree/jstree.min.js");
\core\Html::instance()->setJs("/assets/modules/menu/js/list.js");
?>
