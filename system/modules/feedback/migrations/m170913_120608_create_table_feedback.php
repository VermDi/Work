<?php
use core\Migration;

class m170913_120608_create_table_feedback extends Migration
{
    public function up()
    {
        $this->query('
CREATE TABLE IF NOT EXISTS `feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_form` int(11) NOT NULL,
  `email` varchar(255) NULL DEFAULT NULL,
  `name` varchar(255) NULL DEFAULT NULL,
  `theme` varchar(512) NULL DEFAULT NULL,
  `deleted` int(2) NOT NULL DEFAULT \'0\',
  `info` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `department_id` int(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `department_id` (`department_id`),
  KEY `id_form` (`id_form`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
');
        return true;
    }

    public function down()
    {
        $this->query("DROP TABLE IF EXISTS `feedback`");
        return true;
    }
}