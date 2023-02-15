<?php

use core\Migration;

class m200615_105244_multidomains extends Migration
{
    public function up()
    {
        $this->query("ALTER TABLE `pages`
	CHANGE COLUMN `domain` `domain` VARCHAR(250) NOT NULL DEFAULT '0' AFTER `right_key`;");
        $this->query("ALTER TABLE `pages`
	DROP INDEX `url`,
	ADD INDEX `url` (`url`, `domain`);");
        return true;
    }

    public function down()
    {
        $this->query("ALTER TABLE `pages`
	ALTER `domain` DROP DEFAULT;");
        $this->query("ALTER TABLE `pages`
	CHANGE COLUMN `domain` `domain` VARCHAR(250) NULL AFTER `right_key`;");
        $this->query("ALTER TABLE `pages`	DROP INDEX `uniq-url`;");
        return true;
    }
}