<?php
/**
 * Created by PhpStorm.
 * User: Евгения
 * Date: 28.08.2017
 * Time: 10:54
 */
/**
 * @var $data array
 */
?>
<?if(!empty($data)){?>
    <ul class="nav navbar-nav">
        <?foreach ($data AS $item){?>
            <li<?=($item->url==\core\App::$url['path'])?" class='active'":"";?>><a href="<?=$item->url;?>"><?=$item->name;?></a></li>
        <?}?>

    </ul>

<?}?>
