<script>
    function isMigrate(obj){
        if (obj.value.length>1){
           document.getElementById('isMigration').removeAttribute('disabled');
        } else {
            document.getElementById('isMigration').setAttribute('disabled','disabled');
        }
    }
</script><div class="pull-right"><a class="btn btn-sm btn-primary" onclick="ShowForm('cModule');">Создать модуль</a>
    <a class="btn btn-sm btn-primary" onclick="ShowForm('cTemplate');">Создать Шаблон</a></div>
<?php $folders = \core\FS::instance()
    ->getFoldersInFolder($_SERVER['DOCUMENT_ROOT'] . "/../system/modules");
$res = \core\Db::getPdo()
    ->query("SHOW TABLES")
    ->fetchAll();
?>
<div class="panel dontshow cModule">
    <div class="panel-heading">
        Создаем модуль | [ВНИМАТЕЛЬНО читай подписи к полям]
    </div>
    <div class="panel-body">

        <form action="/gkg/admin/generate" method="post">
            <input type="hidden" name="GKGact" value="module">
            <div class="clear col-sm-12">
                <legend>Модуль и таблица</legend>
                <div class="col-sm-6">
                    <label for="GKGName">Название модуля</label>
                    <input type="text" name="GKGName" class="form-control" required="required" placeholder="ModuleName" value="">
                    <span class="help-block">Название нового модуля, если модуль есть, все разложится в нем</span>
                </div>
                <div class="col-sm-6">
                    <label for="GKGtable">Название таблицы</label>
                    <select name="GKGtable" class="form-control" onchange="loadTable2(this);" required="required">
                        <option value="" selected>-</option>
                        <?
                        foreach ($res as $k => $v) {
                            ?>
                            <option value="<?= $v[0]; ?>"><?= $v[0]; ?></option>
                            <?
                        }
                        ?>
                    </select>
                    <span class="help-block">Она должна быть в подключаемой БД</span>
                </div>
            </div>
            <div class="clear col-sm-12">
                <legend>Что будем создавать</legend>
                <div class="col-sm-3">
                    <label for="GKGmodel_name">Название Модели</label>
                    <input type="text" name="GKGmodel_name" class="form-control" placeholder="Name">
                    <span class="help-block">Если название пустое, то Модель не будет создаваться</span>
                </div>
                <div class="col-sm-3">
                    <label for="GKGcontroller_name">Название Контроллера</label>
                    <input type="text" name="GKGcontroller_name" class="form-control"
                           placeholder="Name" onkeyup="isMigrate(this);">
                    <span class="help-block">Если название пустое, то Контроллер не будет создаваться</span>
                    <input type="checkbox" value="1" name="isMigration" id="isMigration"> - создать пункт меню
                </div>
                <div class="col-sm-3">
                    <label for="GKGtemplate_name">Название Темплейта (ФОРМЫ)</label>
                    <input type="text" name="GKGtemplate_name" class="form-control" placeholder="Name">
                    <span class="help-block">Если название пустое, то Темплейт создаваться не будет</span>
                </div>
                <div class="col-sm-3">
                    <label for="GKGtemplateList_name">Название Темплейта (Списка админа)</label>
                    <input type="text" name="GKGtemplateList_name" class="form-control" placeholder="Name">
                    <span class="help-block">Если название пустое, то Темплейт создаваться не будет</span>
                </div>
            </div>
            <div id="inner_form2" class="col-sm-12"></div>
            <div class="col-sm-12">
                <button class="btn btn-success" type="submit" id="save_form2" style="width: 100%; margin-top: 15px;">
                    Сохранить!
                </button>
            </div>
        </form>
    </div>
</div>

<div class="panel dontshow cTemplate">
    <div class="panel-heading">
        Создаем форму
    </div>
    <div class="panel-body">
        <form action="/gkg/admin/generate" method="post" name="form2">
            <input type="hidden" name="GKGact" value="form">
            <div class="col-sm-3">
                <label for="name">Название Файла</label>
                <input type="text" name="GKGName" class="form-control" required>
                <span class="help-block">Название новой формы</span>
            </div>
            <div class="col-sm-3">
                <label for="GKGfolder">В каком модуле создаем шаблон?</label>
                <select name="GKGfolder" require class="form-control">
                    <option value="" selected>-</option>
                    <?php foreach ($folders as $k => $v) { ?>
                        <option value="<?= $k; ?>"><?= $v; ?></option> <?php } ?>
                </select>
                <span class="help-block">В папке данного модуля создастся темплейт</span>
            </div>
            <div class="col-sm-3">
                <label for="GKGtable">Название таблицы</label>
                <select name="GKGtable" class="form-control" onchange="loadTable(this);">
                    <option value="" selected>-</option>
                    <?
                    foreach ($res as $k => $v) {
                        ?>
                        <option value="<?= $v[0]; ?>"><?= $v[0]; ?></option>
                        <?
                    }
                    ?>
                </select>
                <span class="help-block">Она должна быть в подлключаемой БД</span>
            </div>
            <div id="inner_form" class="col-sm-12"></div>
            <div class="col-sm-12">
                <button class="btn btn-xs btn-success" type="submit"  id="save_form">Сохранить!</button>
            </div>
        </form>
    </div>
</div>

<style>
    .dontshow {
        display: none;
    }
</style>
<script>
    function ShowForm(e) {
        $('.dontshow').hide();
        $('.' + e).show();
    }
    function loadTable(e) {
        v = $(e).val();
        if (v.length>1){v="/"+v;}
        $.ajax({
            url: '/gkg/admin/Loadtablestructer' + v,
            success: function (data) {
                $("#inner_form").html(data);
                $('#save_form').removeAttr('disabled');
            }
        }).fail(function () {
            $("#inner_form").html('НЕТ ТАБЛИЦЫ');
        });
    }
    function loadTable2(e) {
        v = $(e).val();
        if (v.length>1){v="/"+v;}
        $.ajax({
            url: '/gkg/admin/Loadtablestructer' + v,
            success: function (data) {
                $("#inner_form2").html(data);
                $('#save_form2').removeAttr('disabled');

            }
        }).fail(function () {
            $("#inner_form2").html('НЕТ ТАБЛИЦЫ');
        });


    }
</script>
