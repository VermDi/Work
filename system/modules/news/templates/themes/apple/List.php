<?php
/**
 * Created by PhpStorm.
 * User: Семья
 * Date: 08.03.2019
 * Time: 11:15
 */
?>
<div class="clearfix">
    <ul class="news">
        <?
        foreach ($data as $k => $v) {
            ?>
            <li>
                <a href="/news/<?= $v->alias; ?>"><?= $v->name; ?></a>
            </li><?
        } ?>
    </ul>
</div>
