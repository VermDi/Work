<?php
/**
 * Created by PhpStorm.
 * User: Евгения
 * Date: 07.06.2017
 * Time: 18:20
 */
/**
 * @var array $data
 */
?>
<h4><?=\core\Html::instance()->title;?></h4>
<form action="" method="post">
    <input type="hidden" value="<?=$data['role']->id;?>" name="id">
<div class="form-group">
    <label for="name" class="">Название</label>
    <input id="name" value="<?=$data['role']->name;?>" name="name" class="form-control form-control-sm"/>
</div>

    <div class="form-group">
        <label for="description">Описание</label>
        <textarea id="description" name="description" rows="5" class="form-control"><?=$data['role']->description;?></textarea>
    </div>
    <div class="form-group">
        <label for="permissions">Права</label>
        <select class="form-control" multiple="multiple" id="permissions" name="permissions[]">
            <?if(!empty($data['permissions'])){?>
                <?foreach ($data['permissions'] as $permission){?>
                    <option value="<?=$permission->id;?>" <?=(in_array($permission->id,$data['role']->permissions))?'selected="selected"':"";?> data-subtext="<?=$permission->description;?>"><?=$permission->name;?></option>
                <?}?>
            <?}?>
        </select>
    </div>
    <input class="btn btn-success btn-xs" type="submit" value="Сохранить"/>
</form>


