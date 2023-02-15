<?php
/**
 * Created by PhpStorm.
 * User: Евгения
 * Date: 28.03.2018
 * Time: 18:29
 */
/**
 * @var $id integer
 */
?>
<a data-toggle="modal" data-target="#myModal" data-action="/user/properties/edit/<?= $id; ?>" type="button"><i
            class="fa fa-edit"></i></a>
<a data-id="<?=$id;?>" class="delete-property" type="button"><i
            class="fa fa-trash"></i></a>
