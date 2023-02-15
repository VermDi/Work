<?php
/**
 * Created by PhpStorm.
 * User: Евгения
 * Date: 17.08.2017
 * Time: 14:10
 */

namespace modules\block\migrations;


use core\DB;
use modules\migrations\models\Migration;

class Install extends Migration
{

    public function Migrate()
    {
        $pdo = DB::getPdo();
        try {
            $pdo->query('CREATE TABLE IF NOT EXISTS `block` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `title` varchar(128) NOT NULL,
  `content` MEDIUMTEXT NOT NULL,
  `pid` int(10) unsigned DEFAULT \'0\',
  `rights` int(10) unsigned DEFAULT \'0\',
  `type` int(10) unsigned DEFAULT \'0\',
  `is_editor_enabled` tinyint(1) unsigned DEFAULT \'0\',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;');

        } catch (\Exception $e) {
            var_dump($e);
        }
        return true;
    }

    public function RollBack()
    {
        $pdo = DB::getPdo();
        $pdo->query("DROP TABLE IF EXISTS `block`");

        return false;
    }
}