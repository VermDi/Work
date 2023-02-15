<?php
/**
 * @var \core\App $this
 */

$this->event->on('core.template.rendered', function (\core\Html $html) {
	if (!$html->isSystemTemplate()) {
		if (isset($_GET['wysiwyg']) and \core\User::current()->isAdmin()) {
			return $html->render;
		}
		if (!empty(json_decode($_SESSION["user"])->csrf_token)) {
			$html->render = str_ireplace("_CSRF_", ($_SESSION["user"])->csrf_token, $html->render);
		}
	}
});