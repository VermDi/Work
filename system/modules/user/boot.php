<?php
/**
 * Create by e-Mind Studio
 * User: Евгения
 * Date: 26.06.2017
 * Time: 12:58
 */
if (!empty($_SESSION['user_bak_id'])) {
    $this->event->on('core.template.rendered', function(\core\Html $html){
        $renderer = html();
        $over = $renderer->render(__DIR__ . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'login_as_overlay.php');
        $html->render = str_replace('</body>', "$over</body>", $html->render);
    });
}