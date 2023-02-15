<?php
use core\Migration;
                  
class m200703_120811_faq extends Migration
{
     public function up() {
         $this->query("CREATE TABLE IF NOT EXISTS `faq_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `faq_questions_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `answer` mediumtext,
  `date` timestamp NULL DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  `best` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `faq_questions_id` (`faq_questions_id`),
  KEY `user_id` (`user_id`),
  KEY `date` (`date`),
  KEY `status` (`status`),
  KEY `best` (`best`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;");

         $this->query("CREATE TABLE IF NOT EXISTS `faq_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `questions` mediumtext NOT NULL,
  `status` int(11) DEFAULT '0',
  `date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `date` (`date`),
  KEY `user_id` (`user_id`),
  KEY `title` (`title`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;");

         if(class_exists('\modules\menu\services\MenuActions')) {
             \modules\menu\services\MenuActions::addInAdminMenu('/faq/questions/list', 'FAQ');
         }
         return true;
     }
                 
     public function down() {
         $this->query("DROP TABLE IF EXISTS faq_answers");
         $this->query("DROP TABLE IF EXISTS faq_questions");
         return true;
     }
}