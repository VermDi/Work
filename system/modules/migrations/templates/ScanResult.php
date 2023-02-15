<?php
/**
 * Created by PhpStorm.
 * User: Евгения
 * Date: 15.09.2017
 * Time: 9:57
 */
/**
 * @var $data array
 */
?>
<?php if (!empty($data)) { ?>
    <?php foreach ($data AS $key => $item) { ?>
        <div class="alert alert-<?= $item['type']; ?>"><strong><?= $key; ?></strong>><?= $item['message']; ?></div>
    <?php } ?>
<?php } ?>
