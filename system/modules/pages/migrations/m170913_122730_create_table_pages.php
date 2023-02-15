<?php
use core\Migration;

class m170913_122730_create_table_pages extends Migration
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
	`create_at` TIMESTAMP NULL DEFAULT NULL,
	`update_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
	`user_id` BIGINT(20) NULL DEFAULT NULL,
	`level` INT(11) NULL DEFAULT NULL,
	`left_key` INT(11) NULL DEFAULT NULL,
	`right_key` INT(11) NULL DEFAULT NULL,
	`domain` VARCHAR(250) NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	INDEX `visible` (`visible`),
	INDEX `url` (`url`),
	INDEX `level` (`level`),
	INDEX `left_key` (`left_key`),
	INDEX `right_key` (`right_key`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;

      ");
        $this->query("INSERT INTO `pages` (`id`, `title`, `meta_keywords`, `meta_description`, `meta_additional`, `content`, `design`, `menu_name`, `visible`, `url`, `create_at`, `update_at`, `user_id`, `level`, `left_key`, `right_key`, `domain`) VALUES (1, 'Еще одна Первая страница на MindCMS', '', '', '', '<p>Спасибо, что выбрали в качестве системы управления MindCMS.</p>\r\n\r\n<p>Авторизуйтесь и продолжите работу.</p>\r\n\r\n<p>С уважением команда разработчиков MindCMS</p>', 'index', 'Первая страница', 1, '/', '2018-09-22 15:46:31', '2018-09-22 16:08:21', NULL, 0, 1, 2, '');
");
        return true;
    }

    public function down()
    {
        $this->query("DROP TABLE IF EXISTS pages");
        return true;
    }
}