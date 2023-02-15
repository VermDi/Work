<?php
use core\Migration;

class m170913_120819_create_table_feedback_departments extends Migration
{
    public function up()
    {
        $this->query('
CREATE TABLE IF NOT EXISTS `feedback_departments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `department_name` varchar(256) NOT NULL,
  `email` varchar(256) NOT NULL,
  `date_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
');
        return true;
    }

    public function down()
    {
        $this->query("DROP TABLE IF EXISTS `feedback_departments`");
        return true;
    }
}