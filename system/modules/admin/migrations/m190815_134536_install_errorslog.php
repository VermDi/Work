<?php

use core\Migration;

class m190815_134536_install_errorslog extends Migration
{
    public function up()
    {
        return \core\Db::getPdo()->query("CREATE TABLE `admin_errors` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`date_time` TIMESTAMP NULL DEFAULT NULL,
	`error` MEDIUMTEXT NULL,
	`app_state` MEDIUMTEXT NULL,
	PRIMARY KEY (`id`),
	INDEX `date_time` (`date_time`)
)
COMMENT='Таблица для логирования ошибок системы'
ENGINE=InnoDB
;
");

    }

    public function down()
    {
        return  \core\Db::getPdo()->query("DROP TABLE IF EXISTS `admin_errors`");
    }
}