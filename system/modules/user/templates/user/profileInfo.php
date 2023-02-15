<?php
/**
 * Created by PhpStorm.
 * User: Евгения
 * Date: 21.09.2017
 * Time: 17:34
 */
/**
 * @var $data array
 */
?>
<div class="user-form-profile-modal">
    <div class="row">
        <div class="col-md-2">
            <div class="avatar-title">Аватар</div>
            <div class="avatar">
                <div>
                    <img src="<?= $data['avatar']; ?>">
                </div>
            </div>
        </div>
        <div class="col-md-10">

            <div class="row">
                <div class="col-xs-4 text-right"><strong>E-mail</strong></div>
                <div class="col-xs-8"><?= $data['user']->email; ?></div>
            </div>
            <div class="row">
                <div class="col-xs-4 text-right"><strong>ФИО</strong></div>
                <div class="col-xs-8"><?= $data['user']->fio; ?></div>
            </div>
            <div class="row">
                <div class="col-xs-4 text-right"><strong>Телефон</strong></div>
                <div class="col-xs-8"><?= $data['user']->phone_number; ?></div>
            </div>
            <?php if (!empty($properties)) { ?>
                <?php foreach ($properties as $property) { ?>
                    <div class="row">
                        <div class="col-xs-4 text-right"><?= $property->title; ?></div>
                        <div class="col-xs-8"><?= $property->value; ?></div>
                    </div>

                <?php } ?>
            <?php } ?>


        </div>
    </div>
</div>
