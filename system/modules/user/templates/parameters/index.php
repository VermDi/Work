<?php
/**
 * Created by PhpStorm.
 * User: noutbuk
 * Date: 03.07.2018
 * Time: 20:50
 */
?>
<div class="panel">
    <div class="panel-heading">Настройки аватара</div>
    <div class="panel-body">
        <?php if (!empty($_SESSION['error'])) { ?>
            <div class="alert alert-warning alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <strong>Внимание!</strong> <?= $_SESSION['error']; ?><?php unset($_SESSION['error']); ?>
            </div>
        <?php } ?>

        <?php if (is_array($data['params']) and count($data['params'])>=1) { ?>
            <form action="/user/parameters/save" method="post">
                <?php foreach ($data['params'] AS $key => $param) { ?>
                    <div class="form-group">
                        <label for="<?= $key; ?>"><?= $param['title']; ?></label>
                        <input class="form-control" name="<?= $key; ?>" id="<?= $key; ?>"
                               value="<?= $param['value']; ?>"/>
                        <span class="help-block"><?= $param['help-block']; ?></span>
                    </div>
                <?php } ?>
                <input type="submit" value="Сохранить" class="btn btn-success btn-xs">
            </form>
        <?php } else { ?>
            Нет настроек для данного модуля
        <?php } ?>
    </div>
</div>
