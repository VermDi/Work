<?php


namespace modules\amocrm\models;


use core\App;
use core\Model;

class AmoCrmFields extends Model
{
	public $table = 'amocrm_fields';

	static function DeleteField($field_id, $items = false)
	{
		if (is_array($field_id) && count($field_id) > 0) {
			$arrRow = [];
			foreach ($field_id as $key => $val) {
				$arrRow[] .= $val;
			}
			$Id_fields = ' IN (' . implode(',', $arrRow) . ') ';
		}

		App::db()->getPdo()->query("DELETE FROM amocrm_fields WHERE  id_field" . $Id_fields); // удалить опросы

	}

}