<?php

namespace core;

use PDO;

/**
 * Сборщик отладочной информации...
 */
class PDOExt extends PDO
{
    public static $questions = [];

    public function exec($statement)
    {
        self::$questions[] = $statement;
        return parent::exec($statement);
    }

    public function query($statement)
    {
        self::$questions[] = $statement;
        return parent::query($statement);
    }
}

/**
 * Класс хелпер, для работы с БД. Возаращеет конекторы к БД
 * Class DB
 * @package core
 */
class Db
{
    protected static $oldConnection;
    protected static $pdo;

    /**
     * Возвращает экземпляр класса PDO!
     * @return PDO
     */
    public static function instance()
    {
        if (!is_object(self::$pdo)) {
            return self::createPdo();
        } else {
            return self::$pdo;
        }
    }

    /**
     * Возвращает экземпляр класса PDO
     * @return PDO
     */
    public static function getPdo()
    {
        return self::instance();
    }

    /**
     * Создает коннектор к PDO
     * @return PDO
     */
    public static function createPdo()
    {
        if (!defined('_DB_PORT_')) {
            define('_DB_PORT_', '3306');
        }
        try {

            if (isset($_SESSION['CreateKey']) and isset($_GET[$_SESSION['CreateKey']]) and $_SESSION['CreateKey'] == $_GET[$_SESSION['CreateKey']]) {
                $pdo = new PDOExt('mysql:port=' . _DB_PORT_ . ';host=' . _DB_SERVER_, _DB_USER_, _DB_PASS_);
                $pdo->exec("CREATE DATABASE `" . _DB_NAME_ . "`;            
                    GRANT ALL ON `" . _DB_USER_ . "`.* TO '" . _DB_USER_ . "'@'localhost';
                    FLUSH PRIVILEGES;");
                $pdo = null;
            }
            $pdo = new PDOExt('mysql:dbname=' . _DB_NAME_ . ';port=' . _DB_PORT_ . ';host=' . _DB_SERVER_, _DB_USER_, _DB_PASS_);
        } catch (\PDOException $e) {
            if (strpos($e->getMessage(), 'Unknown database')) {
                $_SESSION['CreateKey'] = md5(time() . rand(0, 99));
                echo "Не смог найти базу данных, " . _DB_NAME_ . " попробовать <a href='?" . $_SESSION['CreateKey'] . "=" . $_SESSION['CreateKey'] . "'>создать</a> ? ";
                die();
            }
            echo "<b>Ошибка в файле:</b> " . __FILE__ . "<br>";
            echo "<b>Строка:</b> " . (__LINE__ - 2) . "<br>";
            echo iconv('windows-1251', 'utf-8', $e->getMessage());
            die();

        }
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("SET CHARACTER SET utf8");
        return self::$pdo = $pdo;
    }

    /**
     * Меняем коннектор к базе
     * @param $name - имя базы данных
     * @param $server - адрес сервер
     * @param $user - логин
     * @param $pass - пароль
     */
    public static function changeDb($name, $server, $user, $pass)
    {
        $pdo = new PDOExt('mysql:dbname=' . $name . ';host=' . $server, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec("SET CHARACTER SET utf8");
        self::$oldConnection = self::$pdo;
        self::$pdo = $pdo;
    }

    /**
     * возвращает коннект к ранее существовавшему
     */
    public static function returnConnection()
    {
        if (!self::$oldConnection instanceof PDO) {
            throw new \Exception('НЕТ прошлого коннектора');
        }
        self::$pdo = self::$oldConnection;
    }


}
