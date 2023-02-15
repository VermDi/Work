<?php
use core\Migration;

class m171019_103734_add_table_pages extends Migration
{
    public function up()
    {
        $this->query("CREATE TABLE IF NOT EXISTS `pages` (
	`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
	`title` VARCHAR(250) NULL DEFAULT NULL,
	`meta_keywords` TEXT NULL,
	`meta_description` TEXT NULL,
	`meta_additional` TEXT NULL,
	`content` LONGTEXT NULL,
	`design` VARCHAR(150) NULL DEFAULT NULL,
	`menu_name` VARCHAR(220) NULL DEFAULT NULL,
	`visible` INT(1) NOT NULL DEFAULT '0',
	`url` VARCHAR(250) NULL DEFAULT NULL,
	`create_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
	`update_at` TIMESTAMP NULL DEFAULT NULL,
	`user_id` BIGINT(20) NULL DEFAULT NULL,
	`level` INT(11) NULL DEFAULT NULL,
	`left_key` INT(11) NULL DEFAULT NULL,
	`right_key` INT(11) NULL DEFAULT NULL,
	`domain` VARCHAR(250) NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	INDEX `visible` (`visible`),
	INDEX `url` (`url`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;


      ");
        return true;
    }

    public function down()
    {
        $this->query("DROP TABLE IF EXISTS pages");
        return true;
    }
}