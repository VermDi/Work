<?php
/**
 * Created by PhpStorm.
 * User: Евгения
 * Date: 20.11.2017
 * Time: 16:10
 */

/**
 * @var $data array
 */

use modules\gallery\helpers\ImageUpload;
?>
<?
$image = new ImageUpload($data['module_name'] . DIRECTORY_SEPARATOR . (empty($data['key_id']) ? $data['temp_id'] : $data['key_id'])); ?>
<div id="preview"><?= \core\Html::instance()->render(_MOD_PATH_ . 'gallery/templates/gallery.php', ['images' => $data['images'], 'temp_id' => 0]); ?></div>
<form id="image_form" method="post" enctype="multipart/form-data" action='/gallery/ajaximageupload'
      style="clear:both">
    <input type="hidden" value="<?= $data['key_id']; ?>" name="key_id" id="key_id">
    <input type="hidden" value="<?= $data['module_name']; ?>" name="module_name"
           id="module_name">
    <input type="hidden" value="<?= $data['temp_id']; ?>" id="temp_id" name="temp_id">
    <div id='imageloadstatus' style='display:none'><img src="/assets/modules/gallery/img/loader.gif"
                                                        alt="Загрузка...."/></div>
    <div id='imageloadbutton'>
        <input type="file" name="photos[]" id="photoimg" multiple="true"/>
    </div>
</form>
