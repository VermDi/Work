<?php

use core\Migration;

class m211115_078561_amocrm extends Migration
{
    public function up()
    {
        $this->query("CREATE TABLE `amocrm_fields` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NULL DEFAULT NULL,
	`type` VARCHAR(255) NULL DEFAULT NULL,
	`name_in_form` VARCHAR(255) NULL DEFAULT NULL,
	`is_api_only` INT(11) NULL DEFAULT NULL,
	`id_field` INT(11) NULL DEFAULT NULL,
	`category` INT(11) NULL DEFAULT NULL,
	`code` VARCHAR(255) NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	INDEX `name` (`name`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=9
;
");

        $this->query("CREATE TABLE `amocrm_settings` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`client_id` VARCHAR(255) NOT NULL ,
	`client_secret` VARCHAR(255) NOT NULL,
	`redirect_url` VARCHAR(255) NOT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=9
;
");
        if (class_exists('\modules\menu\services\MenuActions')) {
            \modules\menu\services\MenuActions::addInAdminMenu('/amocrm/admin', 'AmoCrm');
        }
        return true;
    }

    public function down()
    {
        $this->query("DROP TABLE `amocrm_fields` ");
        $this->query("DROP TABLE `amocrm_settings` ");
    }
}