<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 16.02.2019
 * Time: 21:04
 */
?>
<div class="panel">
    <div class="panel-heading">Просмотр истории изменений</div>
    <div class="panel-body">
        <div class="clearfix row">
            <div class="col-sm-2">Дата изменения</div>
            <div class="col-sm-2">#Пользователь</div>
            <div class="col-sm-2">Управление</div>
        </div>
        <?
        foreach ($data as $k => $v) {
            ?>
            <div class="clearfix row">
                <div class="col-sm-2"><?= $v->create_at; ?></div>
                <div class="col-sm-2"><?= $v->user_id; ?></div>
                <div class="col-sm-6">
                    <span
                        class="btn btn-xs btn-success"
                        onclick="show('p<?= $v->id; ?>');">Показать изменения</span>
                    <a href="/pages/admin/rallback/<?= $v->id ?>"
                       class="btn btn-xs btn-warning"
                       onclick="return confirm('Вернуть указанную версию ?');">Применить</a>
                    <a href="/historian/admin/delete/<?= $v->id; ?>" class="btn btn-xs btn-danger">Стереть историю</a>
                </div>
                <div class="col-sm-12 obj hidden" id="p<?= $v->id; ?>">
                    <?php echo "<pre>";
                    print_r(unserialize($v->value));
                    echo "</pre>"; ?>
                </div>
            </div>
            <?
        }
        ?>
    </div>
</div>
<script>
    function GO() {
        return;
    }

    function show($id) {
        let objs = document.querySelectorAll('.obj');
        objs.forEach(function (obj) {
            obj.classList.add('hidden');
        });
        document.getElementById($id).classList.remove('hidden');

    }
</script>