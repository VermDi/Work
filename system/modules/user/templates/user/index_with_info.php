<?php
/**
 * Created by PhpStorm.
 * User: Евгения
 * Date: 14.11.2017
 * Time: 15:09
 */
/**
 * @var array $data
 */
?>
<div class="panel">
    <div class="panel-heading clearfix">
        <div class="col-sm-4 pull-left">Пользователи</div>
        <div class="col-sm-8 pull-right" style="text-align: right;">
            <a href="/user/exportcsv" class="btn btn-xs btn-success">Выгрузить CSV</a>
            <a href="/user_extension/parameters" class="btn btn-xs btn-success">Настройки</a>
            <a href="/user/addform" class="btn btn-xs btn-success">Добавить пользователя</a>
        </div>
    </div>
    <div class="panel-body">
        <?php if (!empty($data['users'])) { ?>
            <table class="table table-striped table-bordered responsive" id="users">
                <thead>
                <tr>
                    <th>#</th>
                    <th>E-mail/Логин</th>
                    <th>Дата регистрации</th>
                    <th>Последняя активность</th>
                    <th>Статус</th>
                    <th>Уровень</th>

                    <th data-orderable="false"></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($data['users'] AS $user) { ?>
                    <tr>
                        <td><?= $user->id; ?></td>
                        <td><?= $user->email; ?></td>
                        <td><?= date('d.m.Y H:i:s', strtotime($user->create_at)); ?></td>
                        <td><?= ($user->login_at != '0000-00-00 00:00:00') ? date('d.m.Y H:i:s', strtotime($user->login_at)) : '-'; ?></td>
                        <td>
                            <span class="label label-<?= \modules\user\models\USER::$classCss[$user->blocked]; ?>"> <?= \modules\user\models\USER::$blocked[$user->blocked]; ?></span>
                        </td>
                        <td><?= $user->level; ?></td>
                        <td>
                            <div class="control-buttons">

                                <a href="/user/addform/<?= $user->id; ?>" data-title="Редактировать"
                                   class="button-control-user"><span class="glyphicon glyphicon-pencil"></span></a>
                                <a href="<?= ($user->blocked == \modules\user\models\USER::BLOCKED_YES) ? "/user/unban/" . $user->id : "/user/ban/" . $user->id; ?>"
                                   class="button-control-user"
                                   data-title="<?= ($user->blocked == \modules\user\models\USER::BLOCKED_YES) ? "Разблокировать" : "Заблокировать"; ?>"><span
                                        class="fa <?= ($user->blocked == \modules\user\models\USER::BLOCKED_YES) ? "fa-unlock-alt" : "fa-unlock"; ?>"></span></a>
                                <?php if (\modules\user\models\USER::current()->id != $user->id) { ?><a
                                    href="/user/loginas/<?= $user->id; ?>" class="button-control-user"
                                    data-title="Логин"><span class="glyphicon glyphicon-log-in"></span></a><?php } ?>


                                <?
                                if ($user->id != 1 and $user->id != \modules\user\models\USER::current()->id) { ?>

                                <a class="button-control-user delete-user" data-id="<?= $user->id; ?>"
                                   data-title="Удалить">
                                        <span class="glyphicon glyphicon-trash"></span></a><?php } ?>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        <?php } ?>
    </div>
</div>