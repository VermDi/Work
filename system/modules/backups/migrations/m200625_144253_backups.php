<?php
use core\Migration;
                  
class m200625_144253_backups extends Migration
{
    public function up() {
        $this->query("CREATE TABLE IF NOT EXISTS `backups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comments` text,
  `file_db` varchar(255) DEFAULT NULL,
  `file_files` varchar(255) DEFAULT NULL,
  `date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `date` (`date`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8;");

        if(class_exists('\modules\menu\services\MenuActions')) {
            \modules\menu\services\MenuActions::addInAdminMenu('/backups', 'Backups');
        }
        return true;
    }

    public function down() {
        $this->query("DROP TABLE IF EXISTS backups");
        return true;
    }
}