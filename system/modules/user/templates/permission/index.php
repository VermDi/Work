<?php
/**
 * Created by PhpStorm.
 * User: Евгения
 * Date: 19.06.2017
 * Time: 12:37
 */
/**
 * @var array $data
 */
?>
<div class="row panel">
    <div class="panel-heading clearfix">
        <div class="col-sm-6 pull-left"><a href="/user">Пользователи</a> &rightarrow; Права</div>
        <div class="col-sm-6 pull-right" style="text-align: right;">
            <a href="/user/permission/form" class="btn btn-success btn-xs">Добавить право</a>
            <button class="btn btn-info btn-xs" data-action="scan"
                    data-loading-text="<span class='glyphicon glyphicon-cog glyphicon-animate'></span> Сканирование...">
                Сканировать
            </button>
        </div>
    </div>
    <div class="panel-body">
        <?php if (!empty($data['permissions'])) { ?>
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th></th>
                    <th>Название</th>
                    <th>Описание</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($data['permissions'] as $permission) { ?>
                    <tr>
                        <td><?= $permission->id; ?></td>
                        <td><?= $permission->name; ?></td>
                        <td><?= $permission->description; ?></td>
                        <td>
                            <a href="/user/permission/form/<?= $permission->id; ?>"><i class="glyphicon glyphicon-pencil"></i> </a>
                            <a href="#" class="delete-permission" data-id="<?= $permission->id; ?>"><i
                                        class="glyphicon glyphicon-trash"></i> </a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        <?php } ?>
    </div>
</div>

<div class="modal fade" id="scanPermissionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Найденные права модулей</h4>
            </div>
            <div class="modal-body">
                <form name="permissionForm" action="" method="post" id="scanPermissionForm">
                    <table id="scanPermissionTable" class="table">

                    </table>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
                <button autocomplete="off" data-action="scanAdd"
                        data-loading-text="<span class='glyphicon glyphicon-cog glyphicon-animate'></span> Сохранение..."
                        type="button"
                        class="btn btn-primary submit">Сохранить
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="scanPermissionResultModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Результат добавления права модулей</h4>
            </div>
            <div class="modal-body">
                <div class="result-content"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>
