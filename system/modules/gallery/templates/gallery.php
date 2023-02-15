<?php
/**
 * Created by PhpStorm.
 * User: Евгения
 * Date: 30.10.2017
 * Time: 14:04
 */

/**
 * @var $data array
 */

use modules\gallery\helpers\ImageUpload;
use core\Imgcache;

//echo"<pre>";print_r($data);echo"</pre>";

?>
<?php if (!empty($data['images'])) {
    $first = true;
    $count = 0;
    ?>
    <input type="hidden" id="temp" value="<?= $data['temp_id']; ?>" name="temp">
    <?php foreach ($data['images'] as $item) {
        $count++;
        ?>
        <div class="gallery-container">
            <img src="<?= Imgcache::getImg($item->id, _ROOT_PATH_ . ImageUpload::$uploaddir  . $item->image_name, 200, 200); ?>"
                 class="imgList"/>
            <div class="gallery-toolbar admin-toolbar" data-id="<?= $item->id; ?>">
                <?php if (!$first) { ?><span class="fa fa-arrow-left change-position-up" data-type="image"></span><?php } ?>
                <?php if (!($count == sizeof($data['images']))) { ?><span
                        class="fa fa-arrow-right change-position-down" data-type="image"></span><?php } ?>
                <span class="fa <?= ($item->is_main == \modules\gallery\models\Image::IS_MAIN) ? "fa-star yellow" : "fa-star-o"; ?> make-favorite"
                      data-id="<?= $item->id; ?>"></span>
                <span class="fa fa-trash image-delete" data-type="image"></span>
            </div>
            <div contenteditable="true"
                 class="image-title"
                 data-id="<?= $item->id; ?>"><?= (!empty($item->title)) ? $item->title : "&nbsp;"; ?></div>
        </div>
        <?
        $first = false;
    } ?>
<?php } ?>