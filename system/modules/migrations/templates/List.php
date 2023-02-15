<?php
/**
 * @var $data array
 */
?>
<div class="panel">
    <div class="panel-heading">Выполненные миграции <div class="pull-right">
            <a class="btn btn-primary btn-xs" href="/admin">В админку</a>
            <button class="btn btn-primary btn-xs migrationCreateForm">Создать</button>
            <button class="btn btn-warning btn-xs migrationsDownForm">Откатить</button>
            <button class="btn btn-info btn-xs" data-action="scan"
                    data-loading-text="<span class='glyphicon glyphicon-cog glyphicon-animate'></span> Сканирование...">
                Сканировать
            </button>

        </div>
    </div>
    <div id="errors"></div>
    <div class="panel-body">
        <table class="table table-hovered migrations">
            <thead>
            <tr>
                <td>ID</td>
                <td>Модуль</td>
                <td>Дата</td>
            </tr>
            </thead>
            <?if(!empty($data)){?>
            <tbody>
                <?foreach ($data AS $item){?>
                    <tr>
                        <td><?=$item->version;?></td>
                        <td><?=$item->module_name;?></td>
                        <td><?=date('H:i:s d.m.Y',$item->apply_time);?></td>
                    </tr>
<?}?>
            </tbody>
            <?}?>
        </table>
    </div>
</div>
<div class="modal fade" id="migrationsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Найденные миграции</h4>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <button data-action="scanAdd"
                        data-loading-text="<span class='glyphicon glyphicon-cog glyphicon-animate'></span> Сохранение..."
                        type="button"
                        class="btn btn-primary submit">Применить
                </button>
            </div>
        </div>
    </div>
</div>
