<?php
/**
 * Created by PhpStorm.
 * User: dulentcov-smishko
 * Date: 14.09.2018
 * Time: 15:51
 */

namespace modules\systemtests\tests;

use core\Tests;


class testTests extends Tests
{
    public function run()
    {
        $this->result = $this->isMoreThan();
        echo "Проверка блока завершена: <br> Успешно <b>" . $this->getGoodTests() . "</b>. С ошибкой <b>" . $this->getBadTests() . "</b> ;";
        $this->resetCounters();
        return $this->result;
    }

    public function isMoreThan()
    {
        try {
            echo "<br>Проверка метода <b>moreThan</b> класса <b>core\Tests</b><br>";
            echo "3 < 4 ";
            $this->testResult($this->moreThan(3, 4), false);
            echo "4 > 3 ";
            $this->testResult($this->moreThan(4, 3), true);
            echo "'abc' > 'abcd' ";
            $this->testResult($this->moreThan('abc', 'abcd'), false);
            echo "'abcd' > 'abc ";
            $this->testResult($this->moreThan('abcd', 'abc'), true);
            echo " Obj не сравнивается [] ";
            $this->testResult($this->moreThan(new \stdClass(), []), false);
        } catch (\Exception $e) {
            echo $e->getMessage();
            $this->result = false;

        }
        return $this->result;
    }



}