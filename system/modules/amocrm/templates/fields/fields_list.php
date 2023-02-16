<?
include_once __DIR__ . '/../menu.php';

?>
<section>
	<div class="panel-heading clearfix">
		<a href="/amocrm/admin/addfield" class="btn btn-success pull-right"><i class="glyphicon glyphicon-plus"></i>Добавить
			поле в AmoCRM</a>
	</div>
	<div class="panel-heading clearfix">
		<h5 class="pull-left text-uppercase">Поля Сделки CRM</h5>
	</div>
	<div class="dataTables_wrapper no-footer TableBlock">
		<table
			class="table table-striped table-advance table-hover table-bordered table-condensed cf  no-footer dataTableAll"
			role="grid" aria-describedby="findTable_info">
			<thead class="cf">
			<tr role="row">
				<th>ID_CRM</th>
				<th>Имя в AmoCRM</th>
				<th>Имя в форме</th>
				<th>Тип</th>
				<th>Код</th>
				<th>Редактирование</th>
				<th>Управление</th>
			</tr>
			</thead>
			<tbody>
			<? foreach ($data as $Row) {
				if ($Row->category == 1) {
					$category = 'Поле Сделки';

					?>
					<tr role="row">
						<td><?= $Row->id_field ?></td>
						<td><?= $Row->name ?></td>
						<td><?= $Row->name_in_form ?></td>
						<td><?= $Row->type ?></td>
						<td><?= $Row->code ?></td>
						<td><?= $Row->is_api_only == 1 ? 'Редактирование только из Api' : 'Редактирование из Api и аккаунта AmoCRM ' ?></td>
						<td>
							<?if($Row->type != 'tracking_data'){?>
							<div class="text-center" style="min-width: 110px;">
								<div class="btn-group">
									<a style="margin-right: 5px;"
									   href="/amocrm/admin/edit/<?= $Row->id ?>"
									   class="btn btn-xs btn-success " title="Редактировать"><i
											class="glyphicon glyphicon-edit"></i></a>
									<a style="margin-right: 5px;"
									   href="/amocrm/admin/delete/<?= $Row->id ?>"
									   class="btn btn-xs btn-danger " title="УДАЛИТЬ"><i
											class="glyphicon glyphicon-trash"></i></a>
								</div>
							</div>
							<?}else{
								echo 'Поле статистики / скрытое';
							}?>
						</td>
					</tr>
				<? }
			} ?>
		</table>
	</div>
	<div class="panel-heading clearfix">
		<h5 class="pull-left text-uppercase">Поля Контакта CRM</h5>
	</div>
	<div class="dataTables_wrapper no-footer TableBlock">
		<table
			class="table table-striped table-advance table-hover table-bordered table-condensed cf  no-footer dataTableAll"
			role="grid" aria-describedby="findTable_info">
			<thead class="cf">
			<tr role="row">
				<th>ID_CRM</th>
				<th>Имя в AmoCRM</th>
				<th>Имя в форме</th>
				<th>Тип</th>
				<th>Код</th>
				<th>Редактирование</th>
				<th>Управление</th>
			</tr>
			</thead>
			<tbody>
			<? foreach ($data as $Row) {
				if ($Row->category == 2) {
					$category = 'Поле Контакта';
					?>
					<tr role="row">
						<td><?= $Row->id_field ?></td>
						<td><?= $Row->name?></td>
						<td><?= $Row->name_in_form ?></td>
						<td><?= $Row->type ?></td>
						<td><?= $Row->code ?></td>
						<td><?= $Row->is_api_only == 1 ? 'Редактирование только из Api' : 'Редактирование из Api и аккаунта AmoCRM ' ?></td>
						<td>
							<?if(!in_array($Row->name,['Email','Телефон','Должность'])){?>
							<div class="text-center" style="min-width: 110px;">
								<div class="btn-group">
									<a style="margin-right: 5px;"
									   href="/<?= $this->app->url['way'][0] ?>/<?= $this->app->url['way'][1] ?>/edit/<?= $Row['id'] ?>"
									   class="btn btn-xs btn-success " title="Редактировать"><i
											class="glyphicon glyphicon-edit"></i></a>
									<a style="margin-right: 5px;"
									   href="/<?= $this->app->url['way'][0] ?>/<?= $this->app->url['way'][1] ?>/delete/<?= $Row['id'] ?>"
									   class="btn btn-xs btn-danger " title="УДАЛИТЬ"><i
											class="glyphicon glyphicon-trash"></i></a>
								</div>
							</div>
							<?}else{
								echo 'Редактирование закрыто';
							}?>
						</td>
					</tr>
				<? }
			} ?>
		</table>
	</div>
</section>
