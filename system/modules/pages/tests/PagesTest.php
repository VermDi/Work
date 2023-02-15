<?php
/**
 * Created by PhpStorm.
 * User: dulentcov-smishko
 * Date: 06.11.2018
 * Time: 15:12
 */

namespace modules\pages\tests;


use core\Tests;
use modules\pages\models\Page;

class PagesTest extends Tests
{
    public function run()
    {

        $page = new Page();
        $page->title = 'COOL_PAGE_NEW_TEST';
        $time_id = $page->save();
        if ($time_id < 1) {
            throw new \Exception("НЕ смог создать страницу");

        }
        $page->clear()->getOne($time_id);
        if ($page->id < 1) {
            throw new \Exception("Созданная страница не нашлась");
        }
        $page->content = "IT'S MY LIFE";
        $page->save();

        $arr = $page->clear()->getOne($time_id);
        if ($arr->content != 'IT\'S MY LIFE') {
            throw new \Exception('У страницы что то не так, контент не сохранился');

        }
        $page->clear()->getOne($time_id);
        if (!$page->delete()) {
            throw new \Exception('Не смог удалить запись');

        }
        $arr = $page->clear()->getOne($time_id);
        if ($arr) {
            throw new \Exception('Страница нашлась, а значит не удалена!');
        }
        //сносим каку, так как если тесты проваливались, то могли быть лишние страницы
        $page->clear()->where('title', '=', 'COOL_PAGE_NEW_TEST')->delete();

        return $this->result;
    }

}