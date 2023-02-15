<?php
/**
 * @var $data array
 */
?>
<div class="panel panel-body">
    <table class="table table-condensed table-hover block-tree tree">
        <thead>
        <tr>
            <th>#</th>
            <th>Название</th>
            <th>Код замены</th>
            <th style="width:260px">
                <a class="btn btn-xs btn-success" href="/block/add"  onclick="loadWindow(this); return false;"><i class="glyphicon glyphicon-plus"></i>
                    добавить</a>
            </th>
        </tr>
        </thead>
        <? if (!empty($data['blocks'])) { ?>
            <?php foreach ($data['blocks'] as $k => $block): ?>
                <tr class="treegrid-<?= $block->id ?> <?= $block->pid ? "treegrid-parent-$block->pid" : '' ?>">
                    <td><?= $block->id ?></td>
                    <td><?= htmlspecialchars($block->title) ?></td>
                    <td>{#<?= htmlspecialchars($block->name) ?>#}</td>
                    <td>
                        <div class="btn-group">
                            <a class="btn btn-xs btn-success" href="/block/add/<?= $block->id ?>" onclick="loadWindow(this); return false;"><i
                                        class="glyphicon glyphicon-plus"></i> добавить</a>
                            <a class="btn btn-xs btn-success" href="/block/copy/<?= $block->id ?>" onclick="loadWindow(this); return false;"><i
                                        class="glyphicon glyphicon-copy"></i> Копировать</a>
                            <a class="btn btn-xs btn-warning" href="/block/edit/<?= $block->id ?>" onclick="loadWindow(this); return false;"><i
                                        class="glyphicon glyphicon-edit"></i> править</a>
                            <a class="btn btn-xs btn-danger" href="/block/delete/<?= $block->id ?>" onclick="loadWindow(this); return false;"><i
                                        class="glyphicon glyphicon-trash"></i> удалить</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach ?>
        <? } ?>
    </table>
</div>
<script>
    $('.block-tree').treegrid({
        expanderExpandedClass: 'glyphicon glyphicon-minus',
        expanderCollapsedClass: 'glyphicon glyphicon-plus'
    });
</script>
