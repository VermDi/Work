<?php
/**
 * Created by PhpStorm.
 * User: Евгения
 * Date: 16.06.2017
 * Time: 12:35
 */
/**
 * @var array $data
 *
 */
use modules\user\helpers\PrintTree;

$printTree = new PrintTree();

array_unshift($data['node'], $data['user']);

?>
<div class="form-group">
    <a href="/user" class="btn btn-success btn-xs">К списку пользователей</a>
</div>
<div class="tree-left">
    <div id="tree" data-id="<?= $data['user']->id; ?>">
        <?
        $printTree->printTree($data['node'], $data['user']->level); ?>
    </div>
</div>
<div class="tree-right">

</div>
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" id="FormModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body" id="mbody">

            </div>

        </div>
    </div>
</div>