<?php

use core\Migration;

class m190304_125729_install extends Migration
{
    public function up()
    {
        \core\Db::instance()->query("CREATE TABLE IF NOT EXISTS `news_categories` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL,
	`alias` VARCHAR(255) NOT NULL,
	`data_create` DATETIME NULL DEFAULT NULL,
	`deleted` INT(2) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	UNIQUE INDEX `alias` (`alias`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
ROW_FORMAT=COMPACT
AUTO_INCREMENT=1
;

");
        \core\Db::instance()->query("CREATE TABLE IF NOT EXISTS `news_article` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`categories_id` INT(11) NOT NULL,
	`name` VARCHAR(255) NOT NULL,
	`alias` VARCHAR(255) NOT NULL,
	`data_create` DATE NOT NULL,
	`title` VARCHAR(255) NOT NULL,
	`data_end` DATE NULL DEFAULT NULL,
	`full_article` TEXT NOT NULL,
	`short_article` TEXT NOT NULL,
	`image` VARCHAR(255) NULL DEFAULT NULL,
	`deleted` INT(2) NOT NULL DEFAULT '0',
	`meta_desc` VARCHAR(255) NULL DEFAULT NULL,
	`meta_keywords` VARCHAR(255) NULL DEFAULT NULL,
	`visible` INT(2) NOT NULL DEFAULT '1',
	PRIMARY KEY (`id`),
	UNIQUE INDEX `alias` (`alias`) USING BTREE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
ROW_FORMAT=COMPACT
;

");
    }

    public function down()
    {
        \core\Db::instance()->query("DROP TABLE IF EXISTS news_article ");
        \core\Db::instance()->query("DROP TABLE IF EXISTS news_categories ");
    }
}