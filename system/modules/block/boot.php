<?php
/**
 * @var \core\App $this
 */
$this->event->on('core.template.rendered', function (\core\Html $html) {
	if (!$html->isSystemTemplate()) {
		$html->render = \modules\block\models\Block::render($html->render);
	}
});