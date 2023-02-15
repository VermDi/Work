<?php
/**
 * Created by PhpStorm.
 * User: Евгения
 * Date: 18.09.2017
 * Time: 10:50
 */
/**
 * @var $data array
 */
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">Создание миграции</h4>
</div>
<div class="modal-body">
    <div class="result"></div>
    <form>
        <div class="form-group-sm">
            <label for="name">Имя миграции</label>
            <input type="text" id="name" name="name" class="form-control"/>
        </div>
        <div class="form-group-sm">
            <label for="module">Модуль</label>
            <select  id="module" name="module" class="form-control">
                <option></option>
                <?foreach ($data['modules'] as $module){?>
                    <option value="<?=$module;?>"><?=$module;?></option>
                <?}?>
            </select>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
    <button data-action="migrationCreate"
            data-loading-text="<span class='glyphicon glyphicon-cog glyphicon-animate'></span> Сохранение..."
            type="button"
            class="btn btn-primary submit migrationCreate">Создать
    </button>
</div>
