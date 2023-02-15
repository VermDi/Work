<?php

use core\Migration;

class m191024_143534_add_position extends Migration
{
    public function up()
    {
        \core\Db::getPdo()->query("ALTER TABLE `pages`
	ADD COLUMN `position` INT NULL DEFAULT '1' COMMENT 'позиция с списке' AFTER `menu_id`;");
        return true;
    }

    public function down()
    {
        \core\Db::getPdo()->query("ALTER TABLE `pages` DROP COLUMN `position` ");
        return true;
    }
}