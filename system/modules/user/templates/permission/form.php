<?php
/**
 * Created by PhpStorm.
 * User: Евгения
 * Date: 19.06.2017
 * Time: 12:49
 */
/**
 * @var array $data
 */
?>
<div class="panel">
    <div class="panel-heading">
        <h4><?= \core\Html::instance()->title; ?></h4>
    </div>
    <div class="panel-body">
        <form action="" method="post">
            <input type="hidden" name="id" value="<?= $data['permission']->id; ?>">
            <div class="form-group">
                <label for="name" class="">Название</label>
                <input id="name" value="<?= $data['permission']->name; ?>" name="name"
                       class="form-control form-control-sm"/>
            </div>

            <div class="form-group">
                <label for="description">Описание</label>
                <textarea id="description" name="description" rows="5"
                          class="form-control"><?= $data['permission']->description; ?></textarea>
            </div>
            <input class="btn btn-success btn-xs" type="submit" value="Сохранить"/>
        </form>
    </div>
</div>