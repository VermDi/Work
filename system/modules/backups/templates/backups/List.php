<?php
$HomeUri = '/';
if (isset($_SERVER['HTTP_HOST'])) {
    $protokol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $HomeUri = $protokol . $_SERVER['HTTP_HOST'];
}
$url = $HomeUri.'/backups/backup/save?comments=auto&db=1&files=1&exclude_folder=www/public/';
?>
<div class="page-header clearfix panel">
    <div class="col-sm-6 pull-left" style="font-size: 17px; padding-top: 4px;">
        <div class="pull-left">
            Создание резервных копий
        </div>
    </div>
    <div class="col-sm-6 pull-right" style="text-align: right;">
        <a href="/backups/backup/form/0/ajax" data-title="Добавление" class="btn btn-xs btn-success ajax">Добавить</a>
    </div>
</div>
<div class="panel-body panel">
    <p>Для создания резервной копии удаленно запустите ссылку<br>
        <a target="_blank" href="<?= $url ?>"><?= $url ?></a></p>
    <table class="table table-striped table-hover table-bordered responsive TableProject">
        <thead>
        <tr>
            <th width="50">#</th>
            <th>Комментарий</th>
            <th>БД</th>
            <th>Файлы</th>
            <th>Дата создания</th>
            <th width="100" data-orderable="false">Управление</th>
        </tr>
        </thead>
    </table>
</div>

<div class="modal fade" id="Form">
    <div class="modal-dialog modal-lg modal-full" style="height: 100%;">
        <div class="modal-content" style="height: 90%; overflow-y: scroll;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="ContentTitle"></h4>
            </div>
            <div class="modal-body clearfix" id="AjaxContent">

            </div>
        </div>
    </div>
</div>
