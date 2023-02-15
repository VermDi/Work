<?php
/**
 * Created by PhpStorm.
 * User: E_dulentsov
 * Date: 30.01.2018
 * Time: 11:42
 */

namespace core;

class Log
{
	private $processNumber = 0;
	private $logerFile;
	private $db            = false;
	public  $currentUserId = null;

	/**
	 * На вход принимает имя файла, в который будем логировать.
	 * Logger constructor.
	 *
	 * @param bool $name
	 */
	public function __construct($name = false)
	{
		$this->processNumber = Tools::generateRandomString();

		if (\modules\user\models\USER::current()->id) {
			$this->currentUserId = \modules\user\models\USER::current()->id;
		}

		if ($name == false) {
			$this->db = true;
		} else {
			if (!file_exists($name)) {
				try {
					if (!file_put_contents($name, "")) {
						Errors::e500('НЕ СМОГ СОЗДАТЬ ФАЙЛ: ' . $name);
					} else {
						$this->logerFile = $name;
					}
				} catch (\Exception $e) {
					print_r($e);
					die();
				}
			} else {
				$this->logerFile = $name;
			}
		}

	}

	public function useDB()
	{
		$this->db = true;
		return $this;
	}

	/**
	 * Добавить текст в лог файл.
	 *
	 * @param $txt
	 */
	public function add($txt)
	{
		if (strlen($txt) > 1) {
			if (!$this->db) {
				if (file_put_contents($this->logerFile, "Process: " . $this->processNumber . ", TIME: " . date("Y-m-d H:i:s", time()) . " MESSAGE: " . $txt . PHP_EOL, FILE_APPEND)) {
					return true;
				}
			} else {
				$sql = "CREATE TABLE `log` (
							`id` INT(11) NOT NULL AUTO_INCREMENT,
							`key` VARCHAR(250) NULL DEFAULT NULL,
							`message` LONGTEXT NULL DEFAULT NULL,
							`user_id` INT(11) NULL DEFAULT NULL,
							`create_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
							PRIMARY KEY (`id`),
							INDEX `key` (`key`)
						)
						COLLATE='utf8_general_ci'
						ENGINE=InnoDB
						AUTO_INCREMENT=1
						;
						";
				try {
					Db::instance()->query("INSERT INTO `realty`.`log` (`key`, `message`, `user_id`) VALUES ('" . $this->processNumber . "', '" . addslashes($txt) . "', '" . $this->currentUserId . "');");
				} catch (\Exception $e) {
					Db::instance()->query($sql);
					Db::instance()->query("INSERT INTO `realty`.`log` (`key`, `message`, `user_id`) VALUES ('" . $this->processNumber . "', '" . addslashes($txt) . "', '" . $this->currentUserId . "');");
				}
			}
		}
		return false;
	}

}