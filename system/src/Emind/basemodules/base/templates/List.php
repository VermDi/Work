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
	<div class="panel-heading clearfix">
		<span class="pull-left">Типы телефонов</span>
		<span class="pull-right">
            <a href="/<?= \core\App::$module; ?>/<?= strtolower(\core\App::$controller); ?>/form"
			   class="btn btn-xs btn-success ajax">Добавить тип</a>
             <a href="/<?= \core\App::$module; ?>/admin"
				class="btn btn-xs btn-success ajax">Устройства</a>
             <a href="/<?= \core\App::$module; ?>/manufacturing"
				class="btn btn-xs btn-success ajax">Производители</a>
        </span>
	</div>
	<div class="panel-body">
		<table class="table table-striped table-hover table-bordered responsive datatable" id="tables">
			<thead>
			<tr>
				<th width="50">#</th>
				<th>Название</th>
				<th>Управление</th>
			</tr>
			</thead>
			<tbody id="tr_servies_body">

			</tbody>
		</table>
	</div>
	<div class="modal fade" id="DomainForm">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="ContentTitle"></h4>
				</div>
				<div class="modal-body clearfix" id="AjaxContent">
					<pre></pre>
				</div>
			</div>
		</div>
	</div>
	<style>
		@media screen and (max-width: 640px) {
			table {
				overflow-x: auto;
				display: block;
			}
		}

		@media screen and (max-width: 600px) {
			table {
				width: 100%;
				min-width: 300px;
			}

			thead {
				display: none;
			}

			tbody {
				width: 100%;
				display: block;
			}

			tr {
				width: 100%;
				display: block;
			}

			tr:nth-of-type(2n) {
				background-color: inherit;
			}

			tr td:first-child {
				background: #f0f0f0;
				font-weight: bold;
				font-size: 1.3em;
			}

			tbody td {
				display: block;
				text-align: center;
			}

			tbody td:before {
				content: attr(data-th);
				display: block;
				text-align: center;
			}
		}

	</style>
</div>
<?php echo \core\App::$controller; ?>
<script>
	document.addEventListener("DOMContentLoaded", function () {

		var table = $('.datatable').DataTable({
			ajax: "/<?=\core\App::$module;?>/<?=strtolower(\core\App::$controller);?>/getlist",
			rowId: 'id',
			"columns": [
				{
					"data": "id"
				},
				{
					"data": "name"
				},
				{
					"data": "control"
				},
			],
			"language": {
				"url": "/assets/vendors/datatables/datatables.ru/datatables_ru.json"
			}
		});
	});
</script>
