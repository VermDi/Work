<?php
/**
 * Created by PhpStorm.
 * User: noutbuk
 * Date: 14.06.2018
 * Time: 20:29
 */
/**
 * @var $permissions array
 * @var $user_permissions array
 */
$first = true;
?>
<style>


</style>
<div style="margin-top: 5px;" class="">
    <a class="btn btn-xs btn-success check-all pull-right">Выбрать все</a>
</div>
<div class="clearfix" style="position: initial;"></div>
<div class="col-xs-3" style="padding-right: 0;">
    <ul class="nav" role="tablist">
		<?php foreach ($permissions as $key => $permission) { ?>
            <li role="presentation" <?= ($first) ? "class=\"active\"" : ""; ?> data-toggle="tab"
                href="#<?= $permission['name']; ?>"><a
                        href="#<?= $permission['name']; ?>"><?= $key; ?></a>
            </li>
			<?
			$first = false;
		} ?>
    </ul>
</div>
<div class="col-xs-9 permissions-tabs">
    <div class="tab-content ">
		<?php $first = true; ?>
		<?php foreach ($permissions as $key => $permission) { ?>
            <div id="<?= $permission['name']; ?>" class="tab-pane <?= ($first) ? "active" : ""; ?>">
                <div style="margin-bottom: 10px">
                    <a href="#" class="btn btn-default btn-xs check-tab-permissions">Отметить все</a>
                </div>
				<?php foreach ($permission['items'] as $item) { ?>

                    <div class="col-sm-6"><input name="permission[]" class="permission-checkbox"
                                                 type="checkbox" <?= ($user_permissions && in_array($item->name, $user_permissions)) ? "checked='checked'" : ""; ?>
                                                 value="<?= $item->id; ?>"> -
						<?= $item->description; ?><br><span
                                style="font-size: 10px;"><?= $item->name; ?></span></div>
				<?php } ?>
            </div>
			<?
			$first = false;
		} ?>

    </div>
</div>

