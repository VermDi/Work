<?php
/**
 * Created by PhpStorm.
 * User: Евгения
 * Date: 09.06.2017
 * Time: 17:51
 */
/**
 * @var array $data
 * @var integer $parent_id
 * @var bool $super_admin
 * @var \modules\user\models\USER $user
 */
//echo"<pre>";print_r($data['user']);echo"</pre>";
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title" id="myModalLabel"><?= ($user->id) ? "Новый пользователь" : "Изменить пользователя"; ?></h4>
</div>
<div class="modal-body">
    <div class="panel-body">
        <form role="form" id="user-form" data-toggle="validator" class="form">
            <input type="hidden" name="id" id="id" value="<?= $user->id; ?>">
            <input type="hidden" name="parent_id"
                   value="<?= (!empty($parent_id)) ? $parent_id : ""; ?>">
            <input type="hidden" name="backUrl" value="<?= (!empty($backUrl)) ? $backUrl : ""; ?>">
			<?php if (\modules\user\models\USER::current()->id == 1 && !$user->id) { ?>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="super_admin" id="super_admin"> Супер-админ
                    </label>
                </div>
			<?php } ?>
            <div class="form-group">
                <label for="email">E-mail</label>
                <div class="input-group col-xs-12">
                    <input class="form-control input-sm " type="email" value="<?= $user->email; ?>" name="email"
                           id="email" required>
                </div>
            </div>
            <div class="form-group">
                <label for="password">Пароль</label>
                <div class="input-group">
                    <input class="form-control input-sm" type="text" value="" name="password" id="password">
                    <span class="input-group-btn">
            <button class="btn btn-default btn-sm" type="button" id="genPass"><i
                        class="glyphicon glyphicon-refresh"></i> </button>
          </span>
                </div>
            </div>
            <div class="form-group">
                <label for="blocked">

                    <input
                            type="checkbox" <?= ($user->blocked == \modules\user\models\USER::BLOCKED_YES) ? "checked='checked'" : ""; ?>
                            name="blocked" id="blocked"> Заблокирован

                </label>

            </div>
            <div class="form-group">
                <label for="role">Группы</label>
                <div class="input-group col-xs-11">
                    <select name="role[]" class="form-control" multiple="multiple" id="role">
						<?php foreach ($roles as $role) { ?>
                            <option
                                    value="<?= $role->id; ?>" <?= (in_array($role->name, $user->roles)) ? "selected='selected'" : ""; ?>>
								<?= ($role->description) ? $role->description : $role->name; ?>
                            </option>
						<?php } ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="permission">Права</label>
                <div class="input-group col-sm-12">
                    <!--                    <select name="permission[]" class="form-control" multiple="multiple" id="permission">-->
                    <!--                        --><?php // foreach ($data['permissions'] as $permission) { ?>
                    <!--                            <option-->
                    <!--                                --><?php //= (in_array($permission->name, $data['user']->permissions)) ? "selected='selected'" : ""; ?>
                    <!--                                    value="--><?php //= $permission->id; ?><!--">-->
                    <!--                                --><?php //= $permission->name; ?>
                    <!--                            </option>-->
                    <!--                        --><?php // } ?>
                    <!--                    </select>-->
					<?php if (!empty($permissions)) { ?>
						<?php \modules\user\widgets\PermissionForm::printForm($permissions, $user->permissions); ?>

					<?php } ?>
                </div>
            </div>
        </form>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-success save-user">Сохранить</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
    </div>
</div>
<script>

</script>