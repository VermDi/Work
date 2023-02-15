<?php
/**
 * Created by PhpStorm.
 * User: Семья
 * Date: 29.12.2018
 * Time: 11:47
 */
?>
<div class="panel">
    <div class="panel-heading" id="buttons_row">
        <?php
        /**
         * @param $keys array
         */
        foreach ($keys as $module_key) {

            ?>
            <button class="btn btn-default btn-xs"><?= $module_key->module_key; ?></button>
            <?php
        }
        ?></div>
    <div class="panel-body">
        <table class="table table-striped table-hover table-bordered responsive datatable" id="tables">
            <thead>
            <tr>
                <th width="50">#</th>
                <th>message</th>
                <th>user_id</th>
                <!--                <th>Управление</th>-->
            </tr>
            </thead>
            <tbody id="tr_servies_body">

            </tbody>
        </table>
    </div>
</div>
<script src="/assets/vendors/e-mindhelpers/EmScript.js"></script>

<script>
    //кнопка нажатая ранее
    var old;
    var table = 1;
    //клик по строке с кнопками
    document.getElementById('buttons_row').onclick = function (e) {
        if (old !== undefined) {
            old.classList.remove('btn-primary');
            old.classList.add('btn-default');
        }
        //кликнутая кнопка становится синей
        var obj = e.target;
        old = obj;
        obj.classList.remove('btn-default');
        obj.classList.add('btn-primary');
        var url = "/<?=\core\App::$module;?>/<?=strtolower(\core\App::$controller);?>/getlist/" + obj.innerText;

        if (table === 1) {
            table = $('.datatable').DataTable({
                ajax: url,
                "order": [[ 0, "desc" ]],
                rowId: 'id',
                "columns": [
                    {
                        "data": "id"
                    },
                    {
                        "data": "message"
                    },
                    {
                        "data": "user_id"
                    }
                    // {
                    //     "data": "control"
                    // },
                ],
                "language": {
                    "url": "/assets/vendors/datatables/datatables.ru/datatables_ru.json"
                }
            });
        } else {
            table.ajax.url(url);
            table.ajax.reload();
        }
    }


</script>