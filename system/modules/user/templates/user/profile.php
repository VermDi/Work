<?php
/**
 * Created by PhpStorm.
 * User: Евгения
 * Date: 23.06.2017
 * Time: 13:18
 */
/**
 * @var array $data
 * @var \modules\user\models\USER $user
 */
//echo"<pre>";print_r(json_decode($_SESSION['user']));echo"</pre>";
?>
<div class="user-form-profile">
    <div class="row">
        <div class="col-md-2"><h4><?= \core\Html::instance()->title; ?></h4></div>
        <div class="col-md-10 text-right"><a class="btn btn-danger btn-xs delete-my-account">Удалить мой аккаунт</a>
        </div>
    </div>

    <div class="col-md-2">
        <div class="avatar-title">Аватар</div>
        <div class="avatar">
            <div>
                <img src="<?= $data['avatar']; ?>">
				<?php if ($data['avatar'] != '/assets/templates/index/images/noavatar100.png') { ?>
                    <div><i class="fa fa-repeat"></i></div>
				<?php } ?>
            </div>
        </div>

        <div class="clearfix">
			<?php \modules\user\widgets\ChildrenTree::printSimpleTree(); ?>
        </div>
		<?php if (\modules\user\models\USER::current()->level <= 2) { ?>
            <div class="clearfix">
                <a href="#" data-target="#source-modal" data-action="/user/add" data-toggle="modal"
                   class="btn btn-xs btn-success">Добавить пользователя</a><br><br>
               <?if($users_count){?> <a href="/user" class="btn btn-xs btn-success">Управление пользователями (для профи)</a><?}?>
            </div>
		<?php } ?>
    </div>
    <div class="col-md-10">

        <div class="col-md-12">
            <form action="/user/profile/change" method="post" enctype="multipart/form-data">
                <div class="form-group-sm <?= (!empty($_SESSION['errors']) && !empty($_SESSION['errors']['email'])) ? 'has-error' : ''; ?>">
                    <label for="email">E-mail</label>
                    <input type="text" name="user[email]" class="form-control check-email" id="email"
                           value="<?= $data['user']->email; ?>">
                    <span class="help-block"><?= (!empty($_SESSION['errors']) && !empty($_SESSION['errors']['email'])) ? $_SESSION['errors']['email'] : '';
						unset($_SESSION['errors']['email']); ?></span>
                </div>
                <div class="form-group-sm">
                    <label for="password">Пароль</label>
                    <input type="password" name="user[password]" class="form-control" id="password" value="">
                    <span class="help-block">Если поле оставить пустым пароль не изменится</span>
                </div>
                <div class="form-group-sm">
                    <label for="fio">ФИО</label>
                    <input type="text" name="user[fio]" class="form-control" id="fio" value="<?= $user->fio; ?>">
                </div>
                <div class="form-group-sm">
                    <label for="phone_number">Номер телефона</label>
                    <input type="tel" name="user[phone_number]" class="form-control" id="phone_number"
                           value="<?= $user->phone_number; ?>">
                </div>
				<?php if (!empty($properties)) { ?>
					<?php foreach ($properties as $property) { ?>
                        <input type="hidden" name="user_prop_ids[<?= $property->id; ?>]"
                               value="<?= $property->prop_id; ?>">
                        <div class="form-group-sm">
                            <label for="<?= $property->name; ?>"><?= $property->title; ?></label>
							<?php if ($property->type === 'text') { ?>
                                <input type="text" name="user_properties[<?= $property->id; ?>]"
                                       class="form-control" id="<?= $property->name; ?>"
                                       value="<?= $property->value; ?>">
							<?php } ?>
							<?php if ($property->type == 'select') { ?>
								<?php $options = json_decode($property->details);
								?>
								<?php $selected_value = (isset($property->value) && !empty($property->value)) ? $property->value : NULL; ?>
                                <select class="form-control input-sm" name="user_properties[<?= $property->id; ?>]">
									<?php $default = (isset($options->default)) ? $options->default : NULL; ?>
									<?php if (isset($options->options)) { ?>
										<?php foreach ($options->options as $index => $option) { ?>
                                            <option value="<?= $index; ?>"
													<?php if ($default == $index && $selected_value === NULL){ ?>selected="selected"<?php } ?>
													<?php if ($selected_value == $index) { ?>selected="selected"<?php } ?>>
												<?= $option; ?>
                                            </option>
										<?php } ?>
									<?php } ?>
                                </select>
							<?php } ?>
                        </div>
					<?php } ?>
				<?php } ?>
                <div class="form-group-sm">
                    <input type="submit" class="btn btn-success btn-xs add-user-button" value="Сохранить">
                </div>
            </form>
        </div>
    </div>
    <div id="source-modal" class="modal fade">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Source Code</h4>
                </div>
                <div class="modal-body">
                    <pre></pre>
                </div>
            </div>
        </div>
    </div>

</div>