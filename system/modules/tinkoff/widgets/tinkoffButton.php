<?php
namespace modules\tinkoff\widgets;

class tinkoffButton
{
	static function instance()
	{
		return new tinkoffButton;
	}
	public function getTinkoffButton(array $data){

		include __DIR__ . '/templates/CredButton.php';
	}
}