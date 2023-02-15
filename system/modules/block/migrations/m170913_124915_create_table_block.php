<?php
use core\Migration;
                  
class m170913_124915_create_table_block extends Migration
{
    public function up()
    {

        $this->query('CREATE TABLE IF NOT EXISTS `block` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `title` varchar(128) NOT NULL,
  `content` text NOT NULL,
  `pid` int(10) unsigned DEFAULT \'0\',
  `rights` int(10) unsigned DEFAULT \'0\',
  `type` int(10) unsigned DEFAULT \'0\',
  `is_editor_enabled` tinyint(1) unsigned DEFAULT \'0\',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;');

    }

    public function down()
    {

        $this->query("DROP TABLE IF EXISTS `block`");
    }
}