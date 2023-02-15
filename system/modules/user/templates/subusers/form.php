<?php
/**
 * Created by PhpStorm.
 * User: E_dulentsov
 * Date: 22.11.2017
 * Time: 15:45
 */

?>
<div class="panel">
    <div class="panel-heading">Добавить клиента</div>
    <div class="panel-body">

        <form action="/user/profile/adduser" method="post" enctype="multipart/form-data">
            <label for="email">Почта человека</label>
            <input name="email" value="" type="text" class="form-control" required>
            <label for="pass">Пароль</label>
            <input name="pass" value="" type="password" class="form-control" required>

            <button type="submit" class="btn btn-success">Добавить</button>
        </form>
    </div>
</div>
