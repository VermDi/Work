<div class="row">
	<div class="col-sm-12 panel-heading">
		<a href="/backupdb/admin/create" onclick="return confirm('Подтвердите создание бэкапа') ? true : false;"
		   class="btn btn-primary">Создать бэкап БД</a>
		<div class="col-sm-12">
			<div class="panel-body">
				<?php if (!empty($data['backupList']))
				{
					?>
					<table class="table table-bordered table-striped table-hover" id="result">
						<thead>
						<tr class="info">
							<td>№</td>
							<td>Дата</td>
							<td>Путь</td>
							<td>Размер</td>
							<td>Управление</td>
						</tr>
						</thead>
						<tbody>
						<?
						$counter = 0;
						foreach ($data['backupList'] as $k => $d)
						{
						$counter++;
						$date = date('d.m.Y H:i:s', $k);
						$file = basename($d);
						$size = $this->getFileSizeHuman(filesize($d));
						?>
						<tr>
							<td><?= $counter; ?></td>
							<td><?= $date; ?></td>
							<td><a href="<?= substr($d,strlen($_SERVER['DOCUMENT_ROOT'])); ?>"><?= substr($d,strlen($_SERVER['DOCUMENT_ROOT'])); ?></a></td>
							<td><?= $size; ?></td>
							<td>
								<a href="/<?= \core\App::$url['way'][0]; ?>/admin/delete/<?= $file; ?>"
								   class="btn btn-xs btn-danger"
								   onclick="return confirm('Подтвердите удаление') ? true : false;">
									<i class="fa fa-trash-o" title="Удалить"></i>
								</a>
								<a href="/<?= \core\App::$url['way'][0]; ?>/admin/restore/<?= $file; ?>"
								   class="btn btn-xs btn-info"
								   onclick="return confirm('Подтвердите восстановление') ? true : false;">
									<i class="fa fa-upload" title="Восстановить"></i>
								</a>
							</td>
							<?php } ?>
						</tr>
						</tbody>
					</table>
				<?php }
				else
				{ ?>
					<div class="warning">Список бэкапов пуст</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
