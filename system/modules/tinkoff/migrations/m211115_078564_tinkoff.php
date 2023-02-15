<?php

use core\Migration;

class m211115_078564_tinkoff extends Migration
{
    public function up()
    {
        $this->query("CREATE TABLE `tinkoff_settings_button` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`SHOP_ID` VARCHAR(255) NOT NULL ,
	`SHOWCASE_ID` VARCHAR(255) NOT NULL,
	`promoCode` VARCHAR(255) NOT NULL DEFAULT 'default',
	`view` VARCHAR(255) NULL,
	`buttonName` VARCHAR(255) NULL,
	`buttonStyle` VARCHAR(255) NULL,
	PRIMARY KEY (`id`)
    )
    COLLATE='utf8_general_ci'
    ENGINE=InnoDB
    AUTO_INCREMENT=9;");

        if (class_exists('\modules\menu\services\MenuActions')) {
            \modules\menu\services\MenuActions::addInAdminMenu('/tinkoff/admin', 'Tinkoff');
        }
        return true;
    }

    public function down()
    {
        $this->query("DROP TABLE `tinkoff_settings_button` ");
    }
}