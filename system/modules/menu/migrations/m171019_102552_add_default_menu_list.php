<?php

use core\Migration;

class m171019_102552_add_default_menu_list extends Migration
{
    public function up()
    {
        $this->query("INSERT INTO `menu` (`id`, `name`, `visible`, `url`, `create_at`, `update_at`, `user_id`, `level`, `left_key`, `right_key`, `domain`, `is_nofollow`, `is_noindex`,`position`) VALUES (1, 'Административное меню', 1, '', NOW(), NOW(), 0, 0, 1, 14, '', 0, 0,1)");
        $this->query("INSERT INTO `menu` (`id`, `name`, `visible`, `url`, `create_at`, `update_at`, `user_id`, `level`, `left_key`, `right_key`, `domain`, `is_nofollow`, `is_noindex`,`position`) VALUES (2, 'Список пользователей', 1, '/user', NOW(), NOW(), 0, 1, 2, 3, '', 0, 0,1)");
        $this->query("INSERT INTO `menu` (`id`, `name`, `visible`, `url`, `create_at`, `update_at`, `user_id`, `level`, `left_key`, `right_key`, `domain`, `is_nofollow`, `is_noindex`,`position`) VALUES (3, 'Админка', 1, '/admin', NOW(), NOW(), 0, 1, 4, 5, '', 0, 0,2)");
        $this->query("INSERT INTO `menu` (`id`, `name`, `visible`, `url`, `create_at`, `update_at`, `user_id`, `level`, `left_key`, `right_key`, `domain`, `is_nofollow`, `is_noindex`,`position`) VALUES (4, 'Страницы', 1, '/pages/admin', NOW(), NOW(), 0, 1, 6, 7, '', 0, 0,3)");
        $this->query("INSERT INTO `menu` (`id`, `name`, `visible`, `url`, `create_at`, `update_at`, `user_id`, `level`, `left_key`, `right_key`, `domain`, `is_nofollow`, `is_noindex`,`position`) VALUES (5, 'Меню', 1, '/menu/admin', NOW(), NOW(), 0, 1, 8, 9, '', 0, 0,4)");
        $this->query("INSERT INTO `menu` (`id`, `name`, `visible`, `url`, `create_at`, `update_at`, `user_id`, `level`, `left_key`, `right_key`, `domain`, `is_nofollow`, `is_noindex`,`position`) VALUES (6, 'Блоки', 1, '/block', NOW(), NOW(), 0, 1, 10, 11, '', 0, 0,5)");
        $this->query("INSERT INTO `menu` (`id`, `name`, `visible`, `url`, `create_at`, `update_at`, `user_id`, `level`, `left_key`, `right_key`, `domain`, `is_nofollow`, `is_noindex`,`position`) VALUES (7, 'Модули (exim)', 1, '/exim', NOW(), NOW(), 0, 1, 12, 13, '', 0, 0,6)");
        return true;
    }

    public function down()
    {

        $this->query('DELETE FROM `menu` WHERE  `id`=7;');
        $this->query('DELETE FROM `menu` WHERE  `id`=6;');
        $this->query('DELETE FROM `menu` WHERE  `id`=5;');
        $this->query('DELETE FROM `menu` WHERE  `id`=4;');
        $this->query('DELETE FROM `menu` WHERE  `id`=3;');
        $this->query('DELETE FROM `menu` WHERE  `id`=2;');
        $this->query('DELETE FROM `menu` WHERE  `id`=1;');
        return true;
    }
}