<?php
/**
 * Created by PhpStorm.
 * User: Евгения
 * Date: 14.09.2017
 * Time: 11:10
 */
/**
 * @var $data array
 */
?>
<form name="migrationsForm" action="" method="post" id="scanMigrationsForm">
    <table id="scanMigrationsTable" class="table">
        <thead>
        <tr>
            <th width="350">Имя</th>
            <th width="90">Модуль</th>
            <th></th>
        </tr>
        </thead>
        <?php if (!empty($data)) { ?>
            <tbody>
            <?php foreach ($data AS $item) { ?>
                <tr id="<?=$item['class'];?>">
                    <td><?= $item['class']; ?></td>
                    <td><?= $item['module']; ?></td>
                    <td><div class="result">
                        </div></td>
                </tr>
            <?php } ?>
            </tbody>
        <?php } ?>
    </table>
</form>