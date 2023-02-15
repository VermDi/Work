<?
include_once($_SERVER['DOCUMENT_ROOT'] . '/../system/src/vendor/autoload.php');
?>
<script src="/assets/modules/debuger/js/debuger.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="/assets/modules/debuger/css/debuger.css">
<div class="coolDebugMindPanel">
    <ul class="DebugPanelHeading">
        <li class="DebugPanelHeading-logo noborder">
            <a href="http://e-mind.ru"> E-mind debug bar</a>
        </li>
        <li id="APP" onclick="showDebugPanel(this)">APP</li>
        <li id="SQL" onclick="showDebugPanel(this);">SQL</li>
        <li id="SESSION" onclick="showDebugPanel(this);">SESSION</li>
        <li id="REQUEST" onclick="showDebugPanel(this);">REQUEST</li>
        <li id="FILES" onclick="showDebugPanel(this);">FILES</li>
        <li id="VIEWS" onclick="showDebugPanel(this);">VIEWS</li>
        <li class="noborder">
            <span style="color: yellow">
                <?= number_format(memory_get_usage() / 1024 / 1024, 2,'.',''); ?> Mb |
                <?= number_format(memory_get_peak_usage() / 1024 / 1024, 2,'.',''); ?> Mb |
                <?= phpversion(); ?> |
                <?= number_format((microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]) * 1000, 2,'.','') . 'ms';
                ?></span>
        </li>
        <li class="DebugPanelHeading-last-btn" id="ShowDebugOrCloseDebug" onclick="closeDebugPanel();">
            <b>&uparrow;&downarrow;</b>
        </li>
    </ul>
    <div id="BodyBlock">
        <div class="DebugPanelBody closeDebugPanel" id="APP_BODY"><!--closeDebugPanel -->
            <?php dump(\core\App::instance());
            dump(json_decode($_SESSION['user']));
            //            dump(get_declared_classes());
            //            dump(get_defined_functions());
            // ?>
        </div>
        <div class="DebugPanelBody closeDebugPanel" id="SESSION_BODY"><!--closeDebugPanel -->
            <?
            dump($_SESSION);
            ?>
        </div>
        <div class="DebugPanelBody closeDebugPanel" id="SQL_BODY"><!--closeDebugPanel -->
            <?php $db = \core\Db::getPdo();
            dump($db::$questions); ?>
        </div>
        <div class="DebugPanelBody closeDebugPanel" id="REQUEST_BODY"><!--closeDebugPanel -->
            <ul class="DebugPanel-widgets-list">
                <li class="list-item"><span class="value error"><?php dump($_GET); ?></span><span class="label">_GET</span>
                </li>
                <li class="list-item"><span class="value error"><?php dump($_POST); ?></span><span
                        class="label">_POST</span></li>
                <li class="list-item"><span class="value error"><?php dump($_SERVER); ?></span><span
                        class="label">_SERVER</span></li>
                <li class="list-item"><span class="value error"><?php dump($_COOKIE); ?></span><span
                        class="label">_COOKIE</span></li>
            </ul>
        </div>
        <div class="DebugPanelBody closeDebugPanel" id="VIEWS_BODY"><!--closeDebugPanel -->
           <?php dump(\core\Html::instance()->getRendered());?>
        </div>
        <div class="DebugPanelBody closeDebugPanel" id="FILES_BODY"><!--closeDebugPanel -->
            <?php dump(get_included_files()); ?>
        </div>
    </div>
</div>

