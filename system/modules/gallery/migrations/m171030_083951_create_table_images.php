<?php

use core\Migration;

class m171030_083951_create_table_images extends Migration
{
    public function up()
    {
        $this->query("CREATE TABLE IF NOT EXISTS `gallery_images` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`module_name` VARCHAR (50) NULL,
	`key_id` INT(11) NULL,
	`temp_id` VARCHAR(50) NULL DEFAULT NULL,
	`image_name` VARCHAR(50) NULL,
	`title` VARCHAR(50) NULL,
	`position` INT(11) NULL,
	`is_main` INT(2) NULL,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;
");
        return true;
    }

    public function down()
    {
        $this->query("DROP TABLE IF EXISTS `gallery_images`;");
        return true;
    }
}