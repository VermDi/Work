<?php


namespace modules\menu\services;


use core\Parameters;

class extParametes
{
	/**
	 * Печатает форму по параметра расширения
	 *
	 * @param null $data
	 */
	public static function print_form($data = null)
	{
		if ($data) {
			$values = json_decode($data);
		}


		if ($arr = Parameters::get('module-menu')) {
			foreach ($arr as $k => $v) {
				if ($v->type == 'checkbox') {
					?>
					<label><?= $v->title; ?></label>
					<input type="checkbox" name="ext-<?= $v->name; ?>"
						   value="<?= (isset($values->{$v->name}) ? $values->{$v->name} : ""); ?>">
					<?php
				} else if ($v->type == 'input') {
					?>
					<label><?= $v->title; ?></label>
					<input type="text" name="ext-<?= $v->name; ?>"
						   value="<?= (isset($values->{$v->name}) ? $values->{$v->name} : ""); ?>" class="form-control">
					<?php
				} else {
					?>
					<label><?= $v->title; ?></label>
					<textarea type="text" name="ext-<?= $v->name; ?>"
							  class="form-control"><?= (isset($values->{$v->name}) ? $values->{$v->name} : ""); ?></textarea>
					<?php
				}
			}
		}

	}

	/**
	 * На вход получает массив ключей и знчений, выделяет из них расширение и готови JSON
	 *
	 * @param $arr
	 *
	 * @return false|string
	 */
	public static function prepareExt($arr)
	{
		$newArr = [];
		foreach ($arr as $k => $v) {
			if (substr($k, 0, 4) == 'ext-') {
				$newArr[substr($k, 4)] = $v;
			}
		}
		return json_encode($newArr);
	}

}