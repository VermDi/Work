<?php
/**
 * Created by PhpStorm.
 * User: Евгения
 * Date: 07.06.2017
 * Time: 14:33
 */
/**
 * @var $data array
 */
//echo"<pre>";print_r($data['roles']);echo"</pre>";
?>
<div class="row panel">
    <div class="panel-heading clearfix">
        <div class="col-sm-6 pull-left"><a href="/user">Пользователи</a> &rightarrow; Группы пользователей</div>
        <div class="col-sm-6 pull-right" style="text-align: right;">
            <a href="/user/role/form" class="btn btn-success btn-xs">Добавить группу</a>
        </div>
    </div>
    <div class="panel-body">
        <?php if (!empty($data['roles'])) { ?>
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
                <?php foreach ($data['roles'] as $role) { ?>
                    <tr>
                        <td><?= $role->id; ?></td>
                        <td><?= $role->name; ?></td>
                        <td><?= $role->description; ?></td>
                        <td>
                            <a href="/user/role/form/<?= $role->id; ?>"><i class="glyphicon glyphicon-pencil"></i> </a>
                            <?php if ($role->id != 1) { ?><a href="#" class="delete-role" data-id="<?=$role->id;?>"><i
                                        class="glyphicon glyphicon-trash"></i> </a><?php } ?>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        <?php } ?>
    </div>
</div>
