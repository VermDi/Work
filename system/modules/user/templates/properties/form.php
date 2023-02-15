<?php
/**
 * Created by PhpStorm.
 * User: Женя
 * Date: 28.03.2018
 * Time: 10:54
 */
/**
 * @var $property \modules\user\models\Property
 */
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
    </button>
    <h4 class="modal-title"
        id="myModalLabel"><?php if ($property->id) { ?>Редактировать свойство<?php } else { ?>Новое свойство<?php } ?></h4>
</div>
<div class="modal-body">
    <form action="" id="form-property">
        <input type="hidden" name="id" id="id" value="<?= $property->id; ?>">
        <div class="form-group">
            <label for="name">Символьное имя</label>
            <div class="input-group col-xs-12">
                <input class="form-control input-sm required" required="required" type="text"
                       value="<?= $property->name; ?>" name="name"
                       placeholder="Только английские буквы, цифры и знак подчеркивания, например: user_name"
                       id="name">
                <span class="help-block"></span>
            </div>
        </div>
        <div class="form-group">
            <label for="title">Заголовок</label>
            <div class="input-group col-xs-12">
                <input class="form-control input-sm required" required="required" type="text"
                       value="<?= $property->title; ?>" name="title"
                       id="title">
                <span class="help-block"></span>
            </div>
        </div>
        <div class="form-group">
            <label for="description">Описание</label>
            <div class="input-group col-xs-12">
                <textarea class="form-control input-sm" name="description" rows="5"
                          id="description"><?= $property->description; ?></textarea>
                <span class="help-block"></span>
            </div>
        </div>
        <div class="form-group">
            <label for="type">Тип</label>
            <div class="input-group col-xs-12">
                <select name="type" id="type" class="form-control">
                    <?php foreach (\modules\user\helpers\UserslHelper::getPropertyTypes() as $key => $value) { ?>
                        <option value="<?= $key; ?>" <?= ($property->type == $key) ? "selected='selected'" : ""; ?>><?= $value; ?></option>
                    <?php } ?>
                </select>
                <span class="help-block"></span>
            </div>
        </div>
        <div class="form-group">
            <a id="toggle_options"><i class="fa fa-angle-double-down"></i> Опции</a>
            <div class="new-settings-options input-group">
                <label for="options">Опции
                    <small>(дейстивтельны только для списка)</small>
                </label>
                <div id="options_editor" class="form-control min_height_200"
                     data-language="json"><?= $property->details; ?></div>
                <textarea id="options_textarea" name="details" class="hidden"><?= $property->details; ?></textarea>
                <div id="valid_options" class="alert-success alert" style="display:none">Верно
                </div>
                <div id="invalid_options" class="alert-danger alert" style="display:none">
                    Ошибка
                </div>
            </div>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
    <button type="button" class="btn btn-primary save-property">Сохранить</button>
</div>
<script>
    $('#toggle_options').click(function () {
        $('.new-settings-options').toggle();
        if ($('#toggle_options .fa-angle-double-down').length) {
            $('#toggle_options .fa-angle-double-down').removeClass('fa-angle-double-down').addClass('fa-angle-double-up');
        } else {
            $('#toggle_options .fa-angle-double-up').removeClass('fa-angle-double-up').addClass('fa-angle-double-down');
        }
      //  $('.toggleswitch').bootstrapToggle();
    });

    var options_editor = ace.edit('options_editor');
    options_editor.getSession().setMode("ace/mode/json");

    var options_textarea = document.getElementById('options_textarea');
    options_editor.getSession().on('change', function () {
       // console.log(options_editor.getValue());
        options_textarea.value = options_editor.getValue();
    });
</script>