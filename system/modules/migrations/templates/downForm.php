<?php
/**
 * Created by PhpStorm.
 * User: Евгения
 * Date: 20.09.2017
 * Time: 15:14
 */
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">Откат миграций</h4>
</div>
<div class="modal-body">
    <div class="result"></div>
    <div class="row">
        <form>
            <div class="form-group-sm col-xs-5">
                <label for="count">Количество последних откатываемых миграций</label>
                <input type="text" id="count" name="count" class="form-control"/>
            </div>
            <div class="form-group-sm col-xs-7">

                <label for="call_date">Дата звонка</label>

                <div class='input-group date' id='datetimepicker1'>
                    <input class="form-control input-sm" type="text" id="call_date" name="call_date" value="">
                    <span class="input-group-addon">
                        <span class="fa fa-calendar"></span>
                    </span>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
    <button data-action="migrationCreate"
            data-loading-text="<span class='glyphicon glyphicon-cog glyphicon-animate'></span> Сохранение..."
            type="button"
            class="btn btn-primary submit migrationsDown">Откатить
    </button>
</div>
