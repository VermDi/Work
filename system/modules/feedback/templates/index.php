<div class="row">
    <div class="col-sm-12 panel-heading">
            <?= $data['topmenu']; ?>    
        <div class="col-sm-12">
            <div class="panel-body">
                <?php if (!empty($data['feedbacks'])) {
                    ?>
                    <table class="table table-bordered table-striped table-hover" id="result">
                        <thead>
                            <tr class="info">
                                <td>№ формы</td>
                                <td>С какой формы обращались</td>
                                <td>Количество обращений</td>
                                <td>Дата последнего обращения</td>
                                <td>Управление</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?
                            foreach ($data['feedbacks'] as $value) {
                                $message = json_decode($value->info, TRUE);
                                ?>
                                <tr>
                                    <td><?= $value->id_form; ?></td>
                                    <td><a href="/feedback/admin/readfeedbacks/<?= $value->form_id; ?>"><?= $value->form_name; ?></a></td>
                                    <td><?= $value->count; ?></td>
                                    <td><?= $value->data; ?></td>
                                    <td>     
                                        <a href="/feedback/admin/delete/<?= $value->form_id; ?>" class="btn btn-xs btn-danger" onclick="return confirm('Вместе с удалением формы, удалятся также и все Feedbacks, Вы уверены?') ? true : false;"><i class="fa fa-trash-o"></i></a>
                                    </td>
                                <?php } ?>
                            </tr>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <div class="warning">Обращений нет!</div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<!--
<form class="form-horizontal" method="POST" action="/feedback/send">
    <fieldset>
        <legend>LEGEND</legend>
        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">Inputname1</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="name" name="Inputname1">
                <input type="hidden" name="form_id" value="10">
                <input type="hidden" name="redirect" value="/news/">
            </div>
    </fieldset>
    <div class="form-group">
        <button type="submit" class="btn btn-success pull-right">SEND</button>
    </div>
</form>-->
