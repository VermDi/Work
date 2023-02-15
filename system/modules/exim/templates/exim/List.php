<?php
include __DIR__ . '/Menu.php';
?>
<div class="panel">
    <div class="panel-heading clearfix">
        <div class="col-sm-4 pull-left">Установленные модули</div>
        <div class="col-sm-8 pull-right" style="text-align: right;"></div>
    </div>
    <div class="panel-body">
        <table class="table table-striped table-hover table-bordered responsive TableModules">
            <thead>
            <tr>
                <th width="150">Название</th>
                <th>Манифест</th>
                <th width="50">Версия</th>
                <th width="200">Инфо</th>
                <th>Описание версии</th>
                <th width="300" data-orderable="false">Управление</th>
            </tr>
            </thead>
        </table>
        <div id="instruction">
            <blockquote>
                <h3 style="margin-bottom: 20px;">Как загрузить модуль на удаленный сервер, инструкция</h3>
                <p>1. Регистрируемся на сайте <a href="http://mind-cms.com/" class="btn btn-xs btn-primary">mind-cms.com</a> и получаем API токен.</p>
                <p>2. Вводим токен на этом сайте <a data-title="Настройки токена" class="btn btn-info ajax"
                                                    href="/exim/formtoken">задать токен</a></p>
                <p>3. Заполняем манифести модуля и нажимаем загрузить в облако</p>
            </blockquote>
        </div>
    </div>
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
