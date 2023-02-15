<p class="menu_exim">
    <a class="btn btn-success" href="/exim/list">Установленные модули</a>
    <?php
    if( \modules\exim\helpers\EximHelper::instance()->checkCurrentToken(['download'=>1])){
        ?><a class="btn btn-success" href="/exim/listserver">Облако модулей</a><?
    } 
    ?>
    <?php
    /*if( \modules\exim\helpers\EximHelper::instance()->checkCurrentToken(['download'=>1,'download_core'=>1])){
        ?><!--<a class="btn btn-success" href="/exim/coredownload">Обновления ядра</a>-->
        <a href="#" type="button" class="btn btn-success" data-toggle="modal" data-target="#VersionModal_core">Обновления ядра</a>
        <?
    }*/
    ?>
    <?php
    /*if( \modules\exim\helpers\EximHelper::instance()->checkCurrentToken(['upload'=>1,'upload_core'=>1])){
        ?><a href="/exim/coreupload/form" data-title="Загрузить ядро" class="btn btn-success ajax">Загрузить ядро</a><?
    }*/
    ?>
    <a data-title="Настройки токена" class="btn btn-info ajax" href="/exim/formtoken">Токен</a>

    <a href="/exim/form" data-title="Добавление" class="btn btn-success ajax">Ручной импорт</a>
    <a href="/migrations" data-title="Добавление" class="btn btn-success">Миграции</a>
    <a href="/gkg/admin" data-title="Добавление" class="btn btn-success">Создать модуль</a>
    <?php
    if (class_exists('modules\backups\models\mBackups')) {
    ?>
        <a href="/backups/backup/list" data-title="Добавление" class="btn btn-success">Backups</a>
        <?
    }
    ?>
</p>
<?php
if(!isset($data['supportSettings']->token)){
    ?><div class="alert alert-danger" role="alert">Токен не найден</div><?
} else {
    if(!\modules\exim\helpers\EximHelper::instance()->checkToken(['token'=>$data['supportSettings']->token])){
        ?><div class="alert alert-danger" role="alert">Токен не работает</div><?
    }
}

/*
 * Проверим есть ли папка www в корне сайта, если нет, то создадим символическую ссылку
 */
if(!\modules\exim\helpers\EximHelper::instance()->checkWWW()){
    ?><div class="alert alert-danger" role="alert">Папка www не найдена, для корректной работы, необходимо создать символическую ссылку www на папку <?= \modules\exim\helpers\EximHelper::instance()->getRootFolder() ?> <a href="#" class="btn btn-info btn-xs CreateWWW">Создать ссылку www</a> </div><?
}

?>
<script type="application/javascript">
    document.querySelectorAll(".menu_exim a").forEach(function(Item) {
       var Url1 = window.location.pathname;
        var Url2 = Item.getAttribute("href").replace(window.location.origin, "");
        if (Url1==Url2) {
            Item.classList.add("active");
        }
    });
</script>
<style>
    .menu_exim a.active{
        background-color: #ece739;
        border-color: #ece739;
        color: #000000;
    }
</style>
