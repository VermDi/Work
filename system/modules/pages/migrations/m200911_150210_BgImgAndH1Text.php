<?php

use core\Migration;

class m200911_150210_BgImgAndH1Text extends Migration
{
	public function up()
	{
		$this->query("ALTER TABLE `pages`
	ADD COLUMN `bg_img` VARCHAR(250) NULL COMMENT 'Фоновая картинка' AFTER `position`,
	ADD COLUMN `h1_text` VARCHAR(250) NULL COMMENT 'Текст для заголовка' AFTER `bg_img`;");
	}

	public function down()
	{
		$this->query("ALTER TABLE `pages`
	DROP COLUMN `bg_img`,
	DROP COLUMN `h1_text`;");
	}
}