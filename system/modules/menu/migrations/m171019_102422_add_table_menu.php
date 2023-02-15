<?php
use core\Migration;
                  
class m171019_102422_add_table_menu extends Migration
{
    public function up()
    {
        $this->query("CREATE TABLE IF NOT EXISTS  `menu` (
	`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(250) NULL DEFAULT NULL,
	`visible` INT(1) NOT NULL DEFAULT '0',
	`url` VARCHAR(250) NULL DEFAULT NULL,
	`create_at` TIMESTAMP NULL DEFAULT NULL,
	`update_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
	`user_id` BIGINT(20) NULL DEFAULT NULL,
	`level` INT(11) NOT NULL DEFAULT '0',
	`left_key` INT(11) NULL DEFAULT NULL,
	`right_key` INT(11) NULL DEFAULT NULL,
	`domain` VARCHAR(250) NULL DEFAULT NULL,
	`is_nofollow` TINYINT(4) NULL DEFAULT NULL,
	`is_noindex` TINYINT(4) NULL DEFAULT NULL,
	`position` INT(11) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`),
	INDEX `visible` (`visible`),
	INDEX `url` (`url`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
AUTO_INCREMENT=0
;

      ");
        return true;
     }
                 
     public function down() {
         $this->query("DROP TABLE IF EXISTS `menu`");
         return true;
     }
}