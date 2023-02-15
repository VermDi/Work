<?php
use core\Migration;
                  
class m190118_144846_lists extends Migration
{
    public function up() {
        $this->query("
CREATE TABLE IF NOT EXISTS `lists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `canonicalName` varchar(50) DEFAULT NULL,
  `pid` int(11) NOT NULL DEFAULT '0',
  `key_item` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`),
  KEY `canonicalName` (`canonicalName`),
  KEY `key_item` (`key_item`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=8192 COMMENT='Списки';
      ");
        return true;
    }

    public function down() {
        $this->query("DROP TABLE IF EXISTS lists");
        return true;
    }
}