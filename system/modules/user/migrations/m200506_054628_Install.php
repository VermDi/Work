<?php

use core\Migration;
use core\Tools;

class m200506_054628_Install extends Migration
{
    public function up()
    {
        $this->query("
         CREATE TABLE `permission` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(128) NOT NULL,
	`description` TEXT NOT NULL,
	`create_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `name` (`name`) USING BTREE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;

         ");
        $this->query("CREATE TABLE `role` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(128) NOT NULL,
	`description` TEXT NULL DEFAULT NULL,
	`create_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `name` (`name`) USING BTREE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;
");

        $this->query("INSERT INTO `role` (`id`, `name`, `description`, `create_at`) VALUES (1, 'admin', 'Администратор системы и вся сего', '2020-05-06 08:57:45');");
        $this->query("INSERT INTO `role` (`id`, `name`, `description`, `create_at`) VALUES (2, 'user', 'Авторизованный пользователь', '2020-05-06 08:58:34');");


        $this->query("CREATE TABLE `role_permission` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`role_id` INT(10) UNSIGNED NOT NULL,
	`permission_id` INT(10) UNSIGNED NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `group_id` (`role_id`, `permission_id`) USING BTREE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;
");
        $this->query("CREATE TABLE `user` (
	`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
	`email` VARCHAR(250) NULL DEFAULT NULL,
	`password` VARCHAR(50) NULL DEFAULT NULL,
	`create_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`login_at` TIMESTAMP NULL DEFAULT NULL,
	`token` VARCHAR(50) NULL DEFAULT NULL,
	`blocked` INT(2) NULL DEFAULT '1',
	`left_key` BIGINT(20) NULL DEFAULT NULL,
	`right_key` BIGINT(20) NULL DEFAULT NULL,
	`level` INT(11) NULL DEFAULT NULL,
	`fio` VARCHAR(255) NULL DEFAULT NULL,
	`phone_number` VARCHAR(25) NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `email` (`email`),
	INDEX `token` (`token`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;
");
        $this->query("CREATE TABLE `user_permission` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`permission_id` INT(10) UNSIGNED NOT NULL,
	`user_id` INT(10) UNSIGNED NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `permission_id` (`permission_id`, `user_id`) USING BTREE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;
");
        $this->query("CREATE TABLE `user_properties` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(50) NOT NULL,
	`title` VARCHAR(50) NOT NULL,
	`description` TEXT NULL DEFAULT NULL,
	`details` TEXT NULL DEFAULT NULL,
	`type` VARCHAR(50) NOT NULL DEFAULT 'text',
	`create_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
	`change_at` TIMESTAMP NULL DEFAULT NULL,
	`owner_id` INT(11) NULL DEFAULT NULL,
	`delete` TINYINT(1) NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	UNIQUE INDEX `name_owner_id` (`name`, `owner_id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;
");
        $this->query("CREATE TABLE `user_property_values` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`user_id` INT(11) NULL DEFAULT NULL,
	`property_id` INT(11) NULL DEFAULT NULL,
	`value` VARCHAR(50) NULL DEFAULT NULL,
	`date_create` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	INDEX `user_id_property_id` (`user_id`, `property_id`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;
");
        $this->query("CREATE TABLE `user_role` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`user_id` INT(10) UNSIGNED NOT NULL,
	`role_id` INT(10) UNSIGNED NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `user_id` (`user_id`, `role_id`) USING BTREE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;
");

        $this->query("INSERT INTO `user_role` (`id`, `user_id`, `role_id`) VALUES (1, 1, 1);");
        $this->query("INSERT INTO `user_role` (`id`, `user_id`, `role_id`) VALUES (4, 2, 2);");


        $this->query("CREATE TABLE `user_token` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`value` VARCHAR(128) NOT NULL,
	`user_id` INT(10) NULL DEFAULT NULL,
	`email` VARCHAR(128) NULL DEFAULT NULL,
	`type` INT(3) NOT NULL,
	`time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `value` (`value`) USING BTREE
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=1
;
");

        $this->query("INSERT INTO `user` (`id`, `email`, `password`, `create_at`, `login_at`, `token`, `blocked`, `left_key`, `right_key`, `level`, `fio`, `phone_number`) VALUES (1, 'admin', '9d7e1d5c368a6ee662545eb970555608bb749228', '2020-05-06 08:57:45', NULL, 'eccbe620fbcbb9255199b567ce952d7a', 1, 1, 8, 1, NULL, NULL);");
        $this->query("INSERT INTO `user` (`id`, `email`, `password`, `create_at`, `login_at`, `token`, `blocked`, `left_key`, `right_key`, `level`, `fio`, `phone_number`) VALUES (2, 'user', '9d7e1d5c368a6ee662545eb970555608bb749228', '2020-05-06 09:29:57', NULL, '8a5d8fc4965ffc54069dee5d2d9d754e', 1, 6, 7, 2, NULL, NULL);");
    }

    public function down()
    {
        $this->query("DROP TABLE IF EXISTS `permission`");
        $this->query("DROP TABLE IF EXISTS `role`");
        $this->query("DROP TABLE IF EXISTS `role_permission`");
        $this->query("DROP TABLE IF EXISTS `user`");
        $this->query("DROP TABLE IF EXISTS `user_role`");
        $this->query("DROP TABLE IF EXISTS `user_token`");
        $this->query("DROP TABLE IF EXISTS `user_permission`");
        $this->query("DROP TABLE IF EXISTS `user_properties`");
        $this->query("DROP TABLE IF EXISTS `user_property_values`");
    }
}