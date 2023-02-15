<?php
use core\Migration;

class m181229_082529_install extends Migration
{
    public function up()
    {
        \core\Db::instance()->query("CREATE TABLE IF NOT EXISTS `logging` (
            `id` BIGINT(20) NOT NULL AUTO_INCREMENT COMMENT 'Ключ',
            `module_key` VARCHAR(250) NULL DEFAULT NULL COMMENT 'Имя модуля',
            `date_time` TIMESTAMP NULL DEFAULT NULL COMMENT 'Время изменени',
            `message` TEXT NULL COMMENT 'Что сделали',
            `user_id` INT(11) NULL DEFAULT NULL COMMENT 'Кто сделал',
            PRIMARY KEY (`id`),
            INDEX `user_id` (`user_id`),
            INDEX `date_time` (`date_time`),
            INDEX `module_key` (`module_key`)
            )
            ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
        ");
        return true;
    }

    public function down()
    {
        \core\Db::instance()->query("DROP TABLE `logging`");
        return true;
    }
}