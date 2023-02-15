<div class="panel panel-body">
    <ol class="breadcrumb">
        <li><a href="/admin">Управление</a></li>
        <li><a href="/block">Блоки</a></li>
    </ol>


    <ul class="nav nav-tabs">
        <li class="active">
            <a data-toggle="tab" href="#block1">Блоки из БД</a>
        </li>
        <li>
            <a data-toggle="tab" href="#block2">Блоки php</a>
        </li>
    </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="block1">
            <div id="blockList">
                <table class="table table-condensed table-hover block-tree tree" id="blockList-table">
                    <thead>
                    <tr>
                        <th></th>
                        <th>#</th>
                        <th>Название</th>
                        <th>Код замены</th>
                        <th style="width:260px">
                            <a class="btn btn-xs btn-success" href="/block/add" onclick="loadWindow(this); return false;"><i
                                        class="glyphicon glyphicon-plus"></i>
                                добавить</a>
                        </th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div> <? /* data-url="/block/table" */ ?>
        </div>
        <div class="tab-pane" id="block2">
            <div class="page-header clearfix">
                <div class="col-sm-6" style="font-size: 17px; padding-top: 4px;">Блоки из Файлов /system/modules/blocks/templates/blocksphp/</div>
            </div>
            <div class="alert alert-primary" role="alert" style="background-color: #cce5ff;">
                Для использования блоков в шаблонах и контенте используйте {!#КодБлока#!}, в блоки из файлов можно передавать параметры {!#КодБлока?id=1&amp;type=2#!}
            </div>
            <?php
            $FilesBlocks = \modules\block\models\mBlocks::instance()->getFilesBlocks();
            ?>
            <div class="PartnersGroupTables">
                <div class="col-sm-12">
                    <table class="table table-striped table-bordered table-hover responsive">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Код</th>
                            <th>Путь</th>
                            <th>Статус</th>
                            <th width="100">#</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $FilesBlocks = \modules\block\models\mBlocks::instance()->getFilesBlocks();
                        foreach ($FilesBlocks as $FilesBlock){
                            ?>
                            <tr>
                                <td>#</td>
                                <td>{!#<?= $FilesBlock['code'] ?>?id=1&type=2#!}</td>
                                <td><?= $FilesBlock['patch'] ?></td>
                                <td>Активный</td>
                                <td></td>
                            </tr>
                            <?
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>



</div>
<div class="modal fade" id="blocks-modal" tabindex="-1" role="dialog" aria-labelledby="blocks-modal"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Изменение блока
            </div>
            <div id="blocks-modal-body" class="modal-body">

            </div>
        </div>
    </div>
</div>
<script>
    function loadWindow(obj) {
        if ($("#blocks-modal-body").load($(obj).attr('href'), function () {
            initForm();
        })) {
            $("#blocks-modal").modal('show');
        }

    }
    function closeWindow(){
        $("#blocks-modal").modal('hide');
    }


</script>