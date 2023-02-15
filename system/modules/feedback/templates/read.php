<div class="row">
	<div class="col-sm-12 panel-heading">
		<?= $data['topmenu'];
		?>
		<div class="col-sm-12">
			<div class="panel-body">
				<?php if (!empty($data['feedbacks'])) {
					?>
					<div class="text-right"><a href="/feedback/admin/deleteall/<?= $data['form_id'] ?>"
											   class="btn btn-danger delete_all_feedback">Удалить все обращения</a>
					</div>
					<table class="table table-bordered table-striped table-hover" id="result">
						<thead>
						<tr class="info">
							<td>№</td>
							<?php foreach ($t = json_decode($data['feedbacks'][0]->form_fields, true) as $key => $value) { ?>
								<td>
									<?= $value['name'] ?>
								</td>
							<?php } ?>
							<td>Дата написания</td>
							<td>Управление</td>

						</tr>
						</thead>
						<tbody>
						<?
						foreach ($data['feedbacks'] as $value) {
						$message = json_decode($value->info, true);

						?>
						<tr>
							<td><?= $value->id; ?></td>
							<?
							reset($t);
							foreach ($t as $k1 => $v1) {
								?>
								<td>
									<?= (isset($message[$v1['name_in_form']]))?$message[$v1['name_in_form']]:''; ?></td>
							<?
							}
							?>
							<td><?= $value->date; ?></td>
							<td>
								<a href="/feedback/admin/deletefeedback/<?= $value->id; ?>"
								   class="btn btn-xs btn-danger"
								   onclick="return confirm('Удалить безвозвратно?') ? true : false;"><i
										class="fa fa-trash-o"></i></a>
							</td>
							<?php } ?>
						</tr>
						</tbody>
					</table>
				<?php } else { ?>
					<div class="warning">Обращений на сайте нет!</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
