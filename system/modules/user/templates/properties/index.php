<?php
/**
 * Created by PhpStorm.
 * User: Женя
 * Date: 28.03.2018
 * Time: 10:41
 */
/**
 * @var array $data
 */
?>
<div class="panel">
    <div class="panel-heading clearfix">
        <div class="col-sm-4 pull-left"><a href="/user">Пользователи</a> &rightarrow;Настройки пользователя</div>
        <div class="col-sm-8 pull-right" style="text-align: right;">
            <a href="#" class="btn btn-xs btn-success" data-target="#myModal" data-action="/user/properties/add"
               data-toggle="modal">Добавить свойство</a>
            <a href="/user/addform" class="btn btn-xs btn-success">Добавить пользователя</a>
            <?php if (\modules\user\models\USER::current()->id == 1) { ?><a href="/user/addform/superadmin"
                                                                         class="btn btn-xs btn-success">Добавить
                админа</a><?php } ?>
            <a href="/user/role" class="btn btn-xs btn-success">Группы</a>
            <a href="/user/permission" class="btn btn-xs btn-success">Права</a>

        </div>
    </div>
    <div class="panel-body">
        <table class="table table-bordered table-responsive table-striped">
            <thead>
            <tr>
                <th data-id="id">#</th>
                <th data-id="name">Имя</th>
                <th data-id="title">Заголовок</th>
                <th data-id="description">Описание</th>
                <th data-id="type">Тип</th>
                <th data-id="buttons" data-orderable="false"></th>
            </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>
</div>
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

        </div>
    </div>
</div>
