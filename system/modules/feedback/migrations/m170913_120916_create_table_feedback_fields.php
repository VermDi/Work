<?php
use core\Migration;
                  
class m170913_120916_create_table_feedback_fields extends Migration
{
     public function up() {
         $this->query('
CREATE TABLE IF NOT EXISTS `feedback_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `fields` text NOT NULL,
  `deleted` int(2) NOT NULL DEFAULT \'0\',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
');
         return true;
     }
                 
     public function down() {
         $this->query("DROP TABLE IF EXISTS `feedback_fields`");
         return true;
     }
}