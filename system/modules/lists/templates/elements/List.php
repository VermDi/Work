<div class="panel">
    <div class="panel-heading clearfix">
        <div class="col-sm-4 pull-left">Элементы списка</div>
        <div class="col-sm-8 pull-right" style="text-align: right;">
            <a href="/lists/elements/form/<?= $data['pid'] ?>/0" data-title="Добавление" class="btn btn-xs btn-success ajax">Добавить элемент</a>
        </div>
    </div>
    <div class="panel-body">
        <ol class="breadcrumb">
            <li><a href="/lists/lists/list">Списки</a></li>
            <?php
            $ParentsListsArr = \modules\lists\models\mLists::instance()->getParentsListsArr($data['pid']);
            foreach ($ParentsListsArr as $List){
                ?><li><a href="/lists/lists/list/<?= $List->id ?>"><?= $List->name ?></a></li><?
            }
            ?>
        </ol>

        <?php
        /*$ChildrenListsArr = \modules\lists\models\mLists::instance()->getChildrenListsTreeArr($data['pid']);
        $ChildrenListsArr = \modules\lists\models\mLists::instance()->getChildrenListsArr($data['pid']);
        echo '<pre>';
        print_r($ChildrenListsArr);
        echo '</pre>';*/
        ?>

        <table data-pid="<?= $data['pid'] ?>" class="table table-striped table-hover table-bordered responsive TableElements">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Наименование элемента</th>
                    <th>Значение элемента</th>
                    <th>Кол-во подэлементов</th>
                    <th data-orderable="false">Управление</th>
                </tr>
                </thead>
            </table>
    </div>
</div>
<div class="modal fade" id="Form">
    <div class="modal-dialog modal-lg modal-full" style="height: 100%;">
        <div class="modal-content" style="height: 90%; overflow-y: scroll;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="ContentTitle"></h4>
            </div>
            <div class="modal-body clearfix" id="AjaxContent">

            </div>
        </div>
    </div>
</div>
