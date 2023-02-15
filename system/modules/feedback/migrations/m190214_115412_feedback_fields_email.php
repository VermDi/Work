<?php
use core\Migration;
                  
class m190214_115412_feedback_fields_email extends Migration
{
     public function up() {
         $this->query("ALTER TABLE `feedback_fields`
	ADD COLUMN `email` VARCHAR(255) NULL DEFAULT NULL AFTER `fields`,
	ADD INDEX `email` (`email`);");
         return true;
     }
                 
     public function down() {
         return true;
     }
}