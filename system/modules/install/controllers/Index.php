<?php
/**
 * Created by PhpStorm.
 * User: Евгения
 * Date: 12.04.2018
 * Time: 13:01
 */

namespace modules\install\controllers;


use core\Controller;
use core\Html;
use PDO;
use PDOException;

class Index extends Controller
{

	public function actionIndex()
	{
		if (file_exists(_SYS_PATH_ . "/defines-local.php")) {
			die('already ready');
		}
		if (!empty($_POST)) {
			$db_name = $_POST['db_name'];
			$db_server = $_POST['db_server'];
			$db_user = $_POST['db_username'];
			$db_pass = $_POST['db_pass'];
			$db_port = $_POST['db_port'];
			$root_path = $_SERVER['DOCUMENT_ROOT'];
			try {
				$pdo = new PDO('mysql:dbname=' . $db_name . ';port=' . _DB_PORT_ . ';host=' . $db_server, $db_user, $db_pass);
				$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$pdo->exec("SET CHARACTER SET utf8");
				$tables = $pdo->query("Show tables from " . $db_name)->fetchAll(\PDO::FETCH_ASSOC);
				if (!$tables) {
					$secret_key = bin2hex(random_bytes(32));
					$nonce = bin2hex(random_bytes(24));
					$content =
						<<<END
<?php

/*
* Настройка подключения к базе данных
*/

define("_USE_DB_", true); // Использовать БД ?
define("_DB_NAME_", "$db_name"); // название базы данных
define("_DB_SERVER_", "$db_server"); // путь к базе данных (сервер)
define("_DB_USER_", "$db_user"); // имя пользователя
define("_DB_PASS_", "$db_pass"); // пароль для пользователя
define("_DB_PORT_", "$db_port"); // лимит подключений к базе данных
define("_DB_LIMIT_", "30"); // лимит подключений к базе данных
define("_PREF_DB_", ""); // ПРЕФИКС БД

/*
 * Установка центрального контроллера, он первым обрабатывает УРЛ.
 */
define("_DEFAULT_MODULE_", "pages"); // Имя модуля по умолчанию, если пусто то смотрится урл /controller/action
define("_IS_SUBDOMAIN_", false); // разрешить ПОД ДОМЕНЫ ?
define("_SUBDOMAINS_", ""); // Доступность поддоменов!!!  Через запятую first,second,...

/*
 * SEO PACK
 */

define("_BASE_DOMAIN_", "realty"); //указывается без зоны, протокола и www. пример http://www.loc.ru, бдует как loc
define("_DOMAIN_", ""); //центальный домен, если он указан то сайт показывается только на нем!
define("_WWW_", false); //если стоит true то домен открывается с www, если стоит false то без
define("_WWW_SLASH_", false); //если true, то путь оканчивается слэшом, если false то без.
define("_WWW_END_", ""); //если не "", то все адреса закачиваются на это окончание!!! НАПРИМЕР .html

/*
 * НАСТРОЙКИ БЕЗОПАСНОСТИ и ПЕРСООНАЛИЗАЦИИ
 */

define("_ROOT_EMAIL_", "info@mind-cms.com"); // Это для системных сообщений, он же логина без БД
define("_ROOT_PASS_", "4bd2aa1e4e8bc573be46273226d89a74"); // ХЭШ ПАРОЛЯ, сделайте его в админке. В случае проблем с БД, его можно будет использовать
define("_SECRET_KEY_", "$secret_key");
define("_NONCE_", "$nonce");


/*
 * ПУТИ И ПАПКИ
 */

define("_ROOT_PATH_", "$root_path");
define("_SYS_PATH_", __DIR__);
define("_MOD_PATH_", _SYS_PATH_ . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR);

/*
 * Настройки почты
 */

/* @var string возможные драйвера: mail, SMTP */
define('_MAIL_DRIVER_', 'mail');
/* @var string например smtp.yandex.ru */
define('_SMTP_HOST_', 'smtp.mail.ru');
/* @var string например 1234@yandex.ru */
define('_SMTP_LOGIN_', 'test@test.ru');
define('_SMTP_PASSWORD_', 'test');
define('_SMTP_SECURE_', 'ssl');
define('_SMTP_PORT_', 25);

/*
 * Права на вновь создаваемые папки и файлы
 */

define("_FILE_R_", 0777); // права на вновь создаваемые файлы
define("_FOLDER_R_", 0777); // права на вновь создаваемые папки

/**
 * настройки мультиязычности
 */
define("_MULTI_LANGUGE_", FALSE);
define("_LANGUGES_", 'rus,eng');
define("_DEFAULT_LANGUGE_", 'rus');
//_CURRENT_LANGUAGE_ - хранит действующий язык

/*
 * Права пользователей по умочанию
 */
define("_SUPER_RIGHT_", 0x7FFFFFFF); // права группы E-mind Team
define("_ADMIN_RIGHT_", 0x7FFFFFFE); // права группы администраторов
define("_USERS_RIGHT_", 0x4); // права группы `пользователей
define("_GUEST_RIGHT_", 0x8); // права группы гостей
/*
 * DEBUG MODE
 */
define("_DEVELOPER_MODE_", true); // режим отладки
/*
 * DESIGN
 */
define("_DEFAULT_TEMPLATE_", "Index"); // Дизайн по умолчанию
END;
					file_put_contents(_SYS_PATH_ . "/defines-local.php", $content);
					echo json_encode(['result' => 'OK']);
				} else {
					echo json_encode(['result' => 'error', 'message' => 'БД не пустая. Очистите БД или задайте другое имя. Либо установите defines-local вручную, скопировав defines ']);
				}
			} catch (\Exception $e) {
				echo json_encode(['result' => 'error', 'message' => $e->getMessage()]);
			}


		} else {
			Html::instance()->setJs('/assets/vendors/Jquery/jquery.progresstimer.min.js');
			Html::instance()->setJs('/assets/modules/install/js/progress.js');
			Html::instance()->content = $this->render('index.php');
			Html::instance()->renderTemplate('@blank')->show();
		}

	}
}