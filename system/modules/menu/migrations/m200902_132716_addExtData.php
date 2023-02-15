<?php

use core\Migration;

class m200902_132716_addExtData extends Migration
{
	public function up()
	{
		$this->query("ALTER TABLE `menu`
	ADD COLUMN `extData` MEDIUMTEXT NULL AFTER `position`;");
	}

	public function down()
	{
		$this->query("ALTER TABLE `menu`
	DROP COLUMN `extData`;");
	}
}