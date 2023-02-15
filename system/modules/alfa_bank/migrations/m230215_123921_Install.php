<?php
use core\Migration;

class m230215_123921_Install extends Migration
{
    public function up()
    {
        //$this->query("");
        if (class_exists('\modules\menu\services\MenuActions')) {
            \modules\menu\services\MenuActions::addInAdminMenu('/alfa_bank/Index', 'Index');
        }
        return true;
    }

    public function down()
    {
        //$this->query("DROP TABLE IF EXISTS ");
        return true;
    }
}