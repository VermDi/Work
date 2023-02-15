<?php

/*
 * Настройка подключения к базе данных
 */

define("_USE_DB_", true); // Использовать БД ?
define("_DB_NAME_", "master"); // название базы данных
define("_DB_SERVER_", "localhost"); // путь к базе данных (сервер)
define("_DB_USER_", "root"); // имя пользователя
define("_DB_PASS_", ""); // пароль для пользователя
define("_DB_PORT_", "3306"); // лимит подключений к базе данных
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
define("_SECRET_KEY_", "!er@er$53"); // не менее 3 символов, нужен для уникализации ВАШЕГО САЙТА.


/*
 * ПУТИ И ПАПКИ
 */

define("_ROOT_PATH_", $_SERVER["DOCUMENT_ROOT"]);
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
 * DEBUg MODE
 */
define("_DEVELOPER_MODE_", true); // режим отладки ?

/*
 * DESIGN
 */
define("_DEFAULT_TEMPLATE_", "Index"); // Доступность поддоменов!!!  Через запятую first,second,...
