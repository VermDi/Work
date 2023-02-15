<?php

namespace modules\tinkoff\helpers;

use core\BaseHelper;
use modules\personal_area\helpers\PersonalAreaReferences;

class TinkoffHelper
{
	public static $viewSel      = [
		'modal'  => 'В модальном окне',
		'newTab' => 'В новой вкладке',
		'self'   => 'Открыть в этой же вкладке',
	];
	public static $buttonStyle  = [
		'TINKOFF_SIZE_M'  => 'Средний размер кнопки',
		'TINKOFF_SIZE_S'  => 'Маленький размер кнопки',
		'TINKOFF_SIZE_L'  => 'Большой размер кнопки',
		'TINKOFF_SIZE_XL' => 'Самый большой размер кнопки',
	];
}