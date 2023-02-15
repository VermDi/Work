<?php

use core\Migration;

class m190216_102458_install extends Migration
{
    public function up()
    {
        \core\Db::getPdo()->query("CREATE TABLE `historian` (
	`id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Первичный ключ',
	`mod_key` VARCHAR(200) NULL DEFAULT NULL COMMENT 'ключ доступа к истории',
	`value` LONGTEXT NULL COMMENT 'значение которые были',
	`create_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'дата обновления',
	`user_id` INT(11) NULL DEFAULT NULL COMMENT 'кто изменил',
	`row_key` VARCHAR(50) NULL DEFAULT NULL COMMENT 'ключ записи модуля',
	PRIMARY KEY (`id`),
	INDEX `mod_key` (`mod_key`),
	INDEX `row_key` (`row_key`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=3
;
");
    }

    public function down()
    {
        \core\Db::getPdo()->query("DROP TABLE `historian`");
    }
}