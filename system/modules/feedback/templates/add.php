<?php $model = $data['model']; ?>
<div class="row">
    <div class="col-sm-12 panel-heading">
            <?= $data['topmenu']; ?>    
        <div class="col-sm-12">
            <div class="panel-heading">
                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="/feedback/admin/addform" id="sumbit_form">
                        <input type="hidden" name="id" value="<?= $model->id; ?>">
                        <fieldset>
                            <legend>Данные формы</legend>
                            <div class="form-group">
                                <label for="name" class="col-sm-2 control-label">Название Формы</label>
                                <div class="col-sm-10">
                                    <?php $name = 'name'; ?>
                                    <input type="text" class="form-control" name="<?= $name; ?>" value="<?= $model->$name; ?>" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="name" class="col-sm-2 control-label">Email уведомлений</label>
                                <div class="col-sm-10">
                                    <?php $name = 'email'; ?>
                                    <input type="text" class="form-control" name="<?= $name; ?>" value="<?= $model->$name; ?>"  >
                                </div>
                            </div>
                        </fieldset>
                        <fieldset class="input_fields_wrap">
                            <legend>Поля формы</legend>
                            <div class="form-group">
                                <label for="name" class="col-sm-2 control-label">Название поля</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="fields[0][name]" >
                                </div>
                                <div class="col-sm-2"><button class="add_field_button">Добавить поле</button></div>
                            </div>
                            <div class="form-group">
                                <label for="name" class="col-sm-2 control-label">Input name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="fields[0][name_in_form]" >
                                </div>
                            </div>
                            <?php
                                $fields = json_decode($model->fields, true);
                                if(is_array($fields)){
                                    foreach($fields as $field){
                                        $keyFils=rand(10000, 99999);
                                        ?>
                                        <div class="additional_field"><hr>
                                            <div class="form-group">
                                                <label for="name" class="col-sm-2 control-label">Название поля</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="fields[<?= $keyFils ?>][name]" value="<?= $field['name'] ?>">
                                                </div><a href="#" class="remove_field">Удалить</a>
                                            </div>
                                            <div class="form-group">
                                                <label for="name" class="col-sm-2 control-label">Input name</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="fields[<?= $keyFils ?>][name_in_form]" value="<?= $field['name_in_form'] ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <?
                                    }
                                }
                            ?>
                        </fieldset>
                        <div class="form-group">
                            <button type="submit" class="btn btn-success pull-right">Сохранить форму</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>