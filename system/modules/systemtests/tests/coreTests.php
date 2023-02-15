<?php
/**
 * Created by PhpStorm.
 * User: dulentcov-smishko
 * Date: 07.11.2018
 * Time: 13:53
 */

namespace modules\systemtests\tests;

use core\Tests;

use League\Flysystem\Exception;
use modules\pages\models\Page;

class coreTests extends Tests
{
    /**
     * Начнем тест ядра системы.. и постараемся его держать в норме
     */
    public function run()
    {
        /*
         * Проверяем модель из ядра.
         */
        $this->checkCoreModelSelect();
        $this->checkCoreModelUpdate();
        $this->checkCoreModelDelete();
    }

    /**
     * Проверка селектов, прошу по мере использования и вставки сложных запросов - включать в проверку.
     * 
     */
    public function checkCoreModelSelect()
    {
        
        /*
         * Простой запрос
         */
        $model = new Page();
        if ($this->similarity($model->where('id', '=', 1)->getQuery(), 'SELECT * FROM pages WHERE id = 1') < 99) {
            throw new Exception('Ошибка запроса' . __LINE__);
        }
        /*
         * Запрос с выборкой
         */
        $model->clear();
        if ($this->similarity($model
                ->where('id', '=', 1)
                ->where('sort', '>=', '50')
                ->getQuery(), 'SELECT * FROM pages WHERE id = 1 and sort >= 50') < 99
        ) {
            throw new Exception('Ошибка запроса' . __LINE__);
        }
        /*
         * Запрос с in
         */
        $model->clear();
        if ($this->similarity(
                $model
                    ->where('id', '=', 1)
                    ->where('sort', '>=', '50')
                    ->limit(100)
                    ->in('Ids', [1, 2, 3])
                    ->getQuery(),
                'SELECT * FROM pages WHERE id = 1 AND sort >= 50 AND Ids IN (1, 2, 3) LIMIT 100') < 99
        ) {
            throw new Exception('Ошибка запроса' . __LINE__);
        }
        /*
         * Запрос с ключевыми словами
         */
        $model->clear();

        if ($this->similarity($model
                ->where('id', '=', 1)
                ->where('sort', '=', NULL)
                ->between('time', 'NOW()', '2019-19-19')
                ->limit(100)
                ->in('Ids', [1, 2, 3])
                ->getQuery(),
                "SELECT * FROM pages WHERE id = 1 AND sort = null AND time BETWEEN NOW() AND '2019-19-19' AND Ids IN (1, 2, 3) LIMIT 100") < 99
        ) {
            throw new Exception('Ошибка запроса' . __LINE__);
        }


    }

    /**
     * Проверка апдейтов
     */
    public function checkCoreModelUpdate()
    {

    }

    /**
     * Проверка удаление
     */
    public function checkCoreModelDelete()
    {

    }

}