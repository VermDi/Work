<div class="col-sm-12">
    <form action="/exim/save" method="post" enctype="multipart/form-data" class="tokenForm">
        <div class="col-sm-12">
            <p>Для того что бы использовать api модулей нужно получить token аккаунта. Для этого вам потребуется зарегистрация на сайте <a target="_blank" href="//mind-cms.com">mind-cms.com</a></p>
            <div>
                <label for="name" class="smalllabel">Введите токен mind-cms</label>
                <input type="text" name="token" class="form-control input-sm" value="<?= (isset($data['supportSettings']->token))?$data['supportSettings']->token:'' ?>" required>
            </div>
        </div>
        <div class="col-sm-12">
            <input type="submit" name="submit" class="btn btn-success col-sm-12" value="Проверить и сохранить токен" style="margin-top: 15px;">
            <?php
            if( \modules\exim\helpers\EximHelper::instance()->checkCurrentToken(['download'=>1])){
                ?><p><a style="margin-top: 20px;" class="btn btn-xs btn-danger removeToken" href="#">Удалить токен</a></p><?
            }
            ?>
        </div>
        <div class="col-sm-12">
            <br>
            <div class="resultTezt"></div>
        </div>
    </form>
</div>