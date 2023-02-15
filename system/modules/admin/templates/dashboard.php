<div class="panel panel-heading">
    <a href="/admin/robots" class="btn btn-default btn-sm">Редактор robots.txt</a> &nbsp;<?
    if (!is_writable(_ROOT_PATH_ . "/" . "public")) {
        ?>
        <i style="color: #F00;">Права не ко всем папкам настроены верно! Возможно
            возникновение ошибок в работе системы!
        </i>
        <?
    } ?>
</div>
<div class="col-sm-12">
    <?php if (class_exists('modules\backupdb\controllers\Admin')) { ?>
        <div class="col-sm-3">
            <div class="panel">
                <div class="panel-heading"><a href="/backupdb/admin" class="btn btn-xs btn-primary">Бэкап
                    </a> - базы данных (DB)
                </div>
                <div class="panel-body">
                    Рекомендуем, делать бэкап Базы данных, хотя бы раз в месяц.
                </div>
            </div>
        </div>
    <?php } ?>
    <?php if (class_exists('modules\gkg\controllers\Admin')) { ?>
        <div class="col-sm-3">
            <div class="panel">
                <div class="panel-heading"><a href="/gkg/admin" class="btn btn-xs btn-primary">Создать модуль
                    </a> - для системы
                </div>
                <div class="panel-body">
                    Мы создали небольшой инструмент, который позволит ВАМ, создавать новые модули быстрее.
                </div>
            </div>
        </div>
    <?php } ?>
    <?php if (class_exists('modules\user\controllers\Profile')) { ?>
        <div class="col-sm-3">
            <div class="panel">
                <div class="panel-heading">Отредактируйте свой <a href="/user/profile" class="btn btn-xs btn-primary">профиль
                    </a>
                </div>
                <div class="panel-body">
                    Регулярно меняйте пароль и следите за обновлениями
                </div>
            </div>
        </div>
    <?php } ?>
    <div class="col-sm-3">
        <div class="panel">
            <div class="panel-heading">Установите <a href="/admin/metrics" class="btn btn-xs btn-primary">метрики
                    сайта</a>
            </div>
            <div class="panel-body">
                Установив метрики от яндекса или гугла вы сможете эффективно отслеживать посетителей своего сайт, не
                создвая лишней нагрузки на сервер.
            </div>
        </div>
    </div>
</div>
<div class="clear">&nbsp;</div><p>&nbsp;</p>
<?php
if (!empty($_SERVER["SERVER_SIGNATURE"])) {
    ?>
    <div class="img-polaroid clear">
        <p><b>Сервер:</b></p>
        <p><?= $_SERVER["SERVER_SIGNATURE"]; ?></p>
    </div>
    <?
}
?>
<div class="col-sm-4">
    <div class="panel">
        <div class="panel-heading">Используемые ресурсы:</div>
        <div class="panel-body">
            <?php echo "Доступный размер диска: <b>" . number_format(((disk_total_space($_SERVER['DOCUMENT_ROOT'])) / 1024 / 1024), 2, ".", " ") . "</b> Mb<br />"
                . "Доступное свободное место: <b>" . number_format((disk_free_space($_SERVER['DOCUMENT_ROOT']) / 1024 / 1024), 2, ".", " ") . "</b> Mb<br />"
                . "Использовано памяти в целом: <b>" . number_format((memory_get_usage() / 1024 / 1024), 2, ".", " ") . "</b> Mb<br />"
                . "Максимальный пик использования памяти: <b>" . number_format((memory_get_peak_usage() / 1024 / 1024), 2, ".", " ") . "</b> Mb"; ?>
        </div>
    </div>
</div>
<div class="col-sm-4">
    <div class="panel">
        <div class="panel-heading">Устновлена версия PHP <?= PHP_VERSION; ?></div>
        <div class="panel-body">
            <div class="clearfix clear"><p>
                    <?php if (file_exists($_SERVER['DOCUMENT_ROOT'])) {
                        ?> Отредактируйте <a href="/admin/robots" class="btn btn-xs btn-warning">robots.txt</a><?
                    } else {
                        ?> Создайте <a href="/admin/robots" class="btn btn-xs btn-danger">robots.txt</a><?
                    } ?></p>
            </div>
            <?php if (!empty($_SERVER["SERVER_ADDR"])) {
                ?>
                <div class="clearfix clear">
                    <p><b>IP адрес сайта:</b></p>
                    <p><?= $_SERVER["SERVER_ADDR"]; ?></p>
                </div>
                <?
            } ?></div>
    </div>

</div>

<div class="col-sm-4">
    <div class="panel">
        <div class="panel-heading">Загруженные пакеты PHP:</div>
        <div class="panel-body">
            <?
            echo implode(" | ", get_loaded_extensions());
            ?>
        </div>
    </div>

</div>
<?php if (class_exists('modules\systemtests\controllers\Index')) { ?>
    <div class="col-sm-4">
        <div class="panel">
            <div class="panel-heading"><a href="/systemtests" class="btn btn-xs btn-primary">Провести тесты
                </a>
            </div>
            <div class="panel-body">
                Данная проверка предназначена для контроля изменений в системе. Если хоть один из тестов не пройден,
                рекомендуем исправить проблему. Так как это может влиять на работу системы в целом, или конкретного
                модуля.
            </div>
        </div>
    </div>
<?php } ?>

<div class="col-sm-4">
    <div class="panel">
        <div class="panel-heading"><a href="/admin/antivirus" class="btn btn-xs btn-primary">Проверка на вирусы
            </a>
        </div>
        <div class="panel-body">
            Базовая проверка на вирусы, она не гарантирует наличие вирусов. А лишь сканирует на подозрительный код
            файлы.
        </div>
    </div>
</div>
<?php if (class_exists('\modules\admin\models\ErrorsLog')) { ?>
    <div class="col-sm-4">
        <div class="panel">
            <div class="panel-heading">Последние ошибки в системе <a href="/admin/cleareroors"
                                                                     class="btn btn-xs btn-primary">Очистить лог</a>
            </div>
            <div class="panel-body">
                <?php $errors = \modules\admin\models\ErrorsLog::instance()->getAll();
                if (count($errors) < 1) {
                    ?> ОТЛИЧНО ОШИБОК НЕТ! <?php } else {
                    echo "<h5>Примерно " . count($errors) . " ошибок:</h5>";
                    foreach ($errors as $err) {
                        echo "<div class='panel-body'>" . $err->error . "</div>";
                    }
                } ?>
            </div>
        </div>
    </div>
<?php } ?>
<?php if (\core\User::current()->isAdmin() and file_exists(__DIR__ . "/Console.php")) {
    include_once(__DIR__ . "/Console.php");
} ?>
