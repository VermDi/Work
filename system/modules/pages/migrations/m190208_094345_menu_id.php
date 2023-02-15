<?php
use core\Migration;
                  
class m190208_094345_menu_id extends Migration
{
     public function up() {
         $this->query("ALTER TABLE `pages`
	ADD COLUMN `menu_id` INT NULL DEFAULT NULL AFTER `domain`,
	ADD INDEX `menu_id` (`menu_id`);
");
         return true;
     }
                 
     public function down() {
         return true;
     }
}