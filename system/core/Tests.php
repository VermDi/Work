<?php
/**
 * Create by e-Mind Studio
 * User: dulentcov-smishko
 * Date: 14.09.2018
 * Time: 16:35
 */

namespace core;


class Tests implements \core\interfaces\Tests
{
    public $result = true;
    public $error = [];
    private $numErrors = 0;
    private $numGoods = 0;
    private $allGoodTests = 0;
    private $allBadTests = 0;
    public static $instance;

    public static function instance()
    {
        return static::$instance ?: new static();
    }

    /**
     * Метод стратует тесты
     */
    public function run()
    {

    }

    /**
     * Принимает две строки и возвращает их схожесть!
     * @param $a
     * @param $b
     * @return bool
     */
    public function similarity($a, $b)
    {

        if (!is_string($a) or !is_string($b)) {
            return false;
        }
        $a = strtolower(trim($a));
        $b = strtolower(trim($b));

        similar_text($a, $b, $percent);
        return $percent;
    }

    /**
     * Метод проверяет что x > y, если сравнить нельзя вернет FALSE
     *
     * @param $x
     * @param $y
     * @return bool
     */
    public function moreThan($x, $y)
    {
        if (is_string($x) and is_string($y)) {
            if (strlen($x) > strlen($y)) {
                return true;
            } else {
                return false;
            }
        }
        if (is_numeric($x) and is_numeric($y)) {
            if ($x > $y) {
                return true;
            } else {
                return false;
            }
        }
        $this->error[] = 'Формат данных не верный, должны быть строка или число.';
        return false;
    }

    /**
     * Метод сранивает резульат работы функции с ожидаемым
     * @param $function
     * @param $result
     */
    public function testResult($function, $result)
    {
        if ($function == $result) {
            echo " - ok<br>";
            $this->numGoods++;
            $this->allGoodTests++;
            $this->result = true;
        } else {
            echo " - err<br>";
            $this->allBadTests++;
            $this->numErrors++;
            $this->result = false;
        }
    }

    /**
     * Возвращает число успешных тестов
     * @return int
     */
    public function getGoodTests()
    {
        return $this->numGoods;
    }

    /**
     * Возвращает число проваленных тестов
     * @return int
     */
    public function getBadTests()
    {
        return $this->numErrors;
    }

    /**
     * Сбрасывает счетчик тестов
     * @return int
     */
    public function resetCounters()
    {
        $this->numErrors = 0;
        $this->numGoods = 0;
    }

}