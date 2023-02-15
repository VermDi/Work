<?php namespace core;

use PHPMailer\PHPMailer\PHPMailer;

/**
 * Набор вспомогательных типовых функций
 * Class Tools
 * @package core
 */
class Tools
{

    protected static $instance;

    public static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Фунция генерации пароля
     * @param $qty - длина генерируемого пароля
     * @return string
     */
    public static function passGenerate($qty = 8)
    {
        $result = "";
        $l = "`1234567890-=\\qwertyuiop[]asdfghjkl;'zxcvbnm,./~!@#$%^&*()_+|QWERTYUIOP{}ASDFGHJKL:\"ZXCVBNM<>?ёйцукенгшщзхъфывапролджэячсмитьбюЁЙЦУКЕНГШЩЗХЪФЫВАПРОЛДЖЭЯЧСМИТЬБЮ ";
        $q = strlen($l);
        for ($i = 0; $i < $qty; $i++) {
            $result .= $l[mt_rand(0, $q - 1)];
        }
        return $result;
    }

    /**
     *  Метод генерирует случайный парольи из переданного массива
     * @param $arr - массив символов из которого будет делать пароль
     * @param $qty - длина пароля
     * @return string возвращает пароль заданной длины
     */
    public static function passFromArr($arr, $qty)
    {
        $pass = "";
        for ($i = 0; $i < $qty; $i++) {
            $index = array_rand($arr);
            $pass .= $arr[$index];
        }
        return $pass;
    }

    /**
     * Метод генерирует случайный парольи из переданной строки
     *
     * @param int $minchars - минималная длина пароля
     * @param int $maxchars - макимальная длина пароля
     * @param string $chars - строка символов
     * @return string - пароль
     */
    public static function passFromString($minchars = 12, $maxchars = 12, $chars = "ABCDEFGHIJKLMNPQRSTUVWXYZ123456789")
    {
        $stringLength = 0;
        $repeat = mt_rand($minchars, $maxchars);
        $pass = '';
        while ($stringLength < $repeat) {
            $pass .= $chars[mt_rand(1, strlen($chars) - 1)];
            $stringLength += 1;
        }
        return $pass;
    }

    /**
     * Фунция отправки почтового сообщения
     * @param $address - email(s) получателей
     * @param $title - заголовок письма
     * @param $message
     * @param string $replyTo - кому отвечать
     * @param array $attachment - документы для отправки
     * @return bool
     * @throws \phpmailerException
     */
    public static function sendMail($address, $title, $message, $replyTo = "", $attachment = array())
    {
        require _SYS_PATH_.'/src/vendor/autoload.php';
        $mail = new PHPMailer();
        if ($replyTo == "") {
            $replyTo = $_SERVER["SERVER_NAME"] . " <info@" . $_SERVER["SERVER_NAME"] . ">";
        }
        $mail->setFrom($replyTo, 'Mailer');
        $mail->addAddress($address);     // Add a recipient
        $mail->addReplyTo($replyTo, 'Information');
        $mail->addCC($replyTo);
        $mail->addBCC($replyTo);
        $mail->CharSet = 'utf-8';
        if (!empty($attachment)) {
            foreach ($attachment as $file_name => $file_path) {
                $mail->addAttachment($file_path);
            }
        }
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = $title;
        $mail->Body = $message;
        if (!$mail->send()) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Фунция отправки почтового сообщения через SMTP сервер
     * @param $params
     *    array params:
     *    server - адрес сервера
     *       port - порт сервера
     *    user - пользователь
     *    password - пароль
     *    to_email - e-mail получателя
     *    to_name - имя получателя
     *    from_email - e-mail отправителя
     *    from_name - имя отправителя
     *    reply_to_email - e-mail отправителя
     *    reply_to_name - имя отправителя
     *    title - текст заголовка сообщения
     *    message - текст сообщения
     * @return array
     * @throws \phpmailerException
     */
    public static function sendSMTPmail($params)
    {
        $result = array(
            "result" => false,
            "info" => ""
        );
        if (!empty($params)) {
            require _SYS_PATH_.'/src/vendor/autoload.php';
            $m = new PHPMailer();
            $m->SMTPDebug = 0;
            $m->CharSet = 'utf-8';
            $m->isSMTP(); // Set mailer to use SMTP
            $m->Host = $params["server"]; // Specify main and backup SMTP servers
            if (isset($params["user"]) && $params["user"] != "" && isset($params["password"])) {
                $m->SMTPAuth = true; // Enable SMTP authentication
                $m->Username = $params["user"]; // SMTP username
                $m->Password = $params["password"]; // SMTP password
            } else {
                $m->SMTPAuth = false; // Disable SMTP authentication
            }
            $m->Port = (isset($params["port"]) ? $params["port"] : 25); // TCP port to connect to (25, 465 with SSL or 587 with TLS)
            if ($m->Port == 465) {
                $m->SMTPSecure = 'ssl'; // Enable SSL encryption
            } else {
                if ($m->Port == 587) {
                    $m->SMTPSecure = 'tls'; // Enable TLS encryption
                } else {
                    if ($m->Port != 25) {
                        $m->Port = 25;
                    }
                }
            }
            if (isset($params["to_email"]) && $params["to_email"] != "") {
                $m->addAddress($params["to_email"], (isset($params["to_name"]) ? $params["to_name"] : "")); // Add a recipient
            }
            if (isset($params["from_email"]) && $params["from_email"] != "") {
                $m->setFrom($params["from_email"], (isset($params["from_name"]) ? $params["from_name"] : ""));
            }
            if (isset($params["reply_to_email"]) && $params["reply_to_email"] != "") {
                $m->addReplyTo($params["reply_to_email"], (isset($params["reply_to_name"]) ? $params["reply_to_name"] : ""));
            }
            $m->isHTML(true); // Set email format to HTML
            $m->Subject = (isset($params["title"]) ? $params["title"] : "Новое сообщение с сайта " . $_SERVER["SERVER_NAME"]);
            $m->Body = (isset($params["message"]) ? $params["message"] : "");
            if ($m->send()) {
                $result["result"] = true;
                $result["info"] = "OK";
            } else {
                $result["info"] = $m->ErrorInfo;
                if (false !== ($fh = fopen(__DIR__ . DIRECTORY_SEPARATOR . "smtp_error.log", "w"))) {
                    fwrite($fh, $m->ErrorInfo . "\n");
                    fclose($fh);
                }
            }
        } else {
            $result["info"] = "Ошибка! Не переданы параметры.";
        }
        return $result;
    }

    /**
     * cyrToLat - функция преобразует строку на русском в строку на латинице транслит)
     * @param $str - строка для конвератации
     * @return string
     */
    public static function cyrToLat($str)
    {
        $tr = [
            "Ґ" => "G",
            "Ё" => "YO",
            "Є" => "E",
            "Ї" => "YI",
            "І" => "I",
            "і" => "i",
            "ґ" => "g",
            "ё" => "yo",
            "№" => "#",
            "є" => "e",
            "ї" => "yi",
            "А" => "A",
            "Б" => "B",
            "В" => "V",
            "Г" => "G",
            "Д" => "D",
            "Е" => "E",
            "Ж" => "ZH",
            "З" => "Z",
            "И" => "I",
            "Й" => "Y",
            "К" => "K",
            "Л" => "L",
            "М" => "M",
            "Н" => "N",
            "О" => "O",
            "П" => "P",
            "Р" => "R",
            "С" => "S",
            "Т" => "T",
            "У" => "U",
            "Ф" => "F",
            "Х" => "H",
            "Ц" => "TS",
            "Ч" => "CH",
            "Ш" => "SH",
            "Щ" => "SCH",
            "Ъ" => "'",
            "Ы" => "YI",
            "Ь" => "",
            "Э" => "E",
            "Ю" => "YU",
            "Я" => "YA",
            "а" => "a",
            "б" => "b",
            "в" => "v",
            "г" => "g",
            "д" => "d",
            "е" => "e",
            "ж" => "zh",
            "з" => "z",
            "и" => "i",
            "й" => "y",
            "к" => "k",
            "л" => "l",
            "м" => "m",
            "н" => "n",
            "о" => "o",
            "п" => "p",
            "р" => "r",
            "с" => "s",
            "т" => "t",
            "у" => "u",
            "ф" => "f",
            "х" => "h",
            "ц" => "ts",
            "ч" => "ch",
            "ш" => "sh",
            "щ" => "sch",
            "ъ" => "",
            "ы" => "yi",
            "ь" => "",
            "э" => "e",
            "ю" => "yu",
            "я" => "ya",
            " " => "-",
            "'" => "",
            "\"" => "",
            "\\\\" => "",
            "." => "-",
            "\/" => ""
        ];
        $in = strtolower(substr(strtr(trim($str), $tr), 0, 220));
        $in = preg_replace("/[`~!#$%^&*()=+\\\\|\\[\\]{};:\"',<>?]+/", "", $in);
        if (substr($in, -1) == "/" && strlen($in) > 1) {
            $in = substr($in, 0, -1);
        }
        //ФИНАЛЬНАЯ ПРОВЕКА
        $TR2 = array(
            "--" => "-",
            "---" => "-",
            "----" => "-"
        );
        return strtr($in, $TR2);
    }

    /**
     * Функия преобразует строку на русском в английси текст через службы гугл перевода!
     */
    public static function RusToEng($title)
    {
        $result = $title;
        $url = "http://ajax.googleapis.com/ajax/services/language/translate?v=1.0&q=" . urlencode($title) . "&langpair=ru%7Cen";
        if (($translate = file_get_contents($url)) !== false) {
            $json = json_decode($translate, true);
            if ($json["responseStatus"] == 200) {
                $result = strtolower(
                    trim(
                        preg_replace(
                            array(
                                "~\W~",
                                "~-+~"
                            ), array(
                            "-",
                            "-"
                        ), stripslashes(htmlspecialchars_decode($json["responseData"]["translatedText"]))
                        ), "-"
                    )
                );
            }
        }
        return $result;
    }

    /**
     * Функция конвертирует арабское число в римское
     * @param $value
     * @return string
     */
    function ArabicToRim($value)
    {
        if ($value < 0) {
            return "";
        }
        if (!$value) {
            return "0";
        }
        $thousands = (int)($value / 1000);
        $value -= $thousands * 1000;
        $result = str_repeat("M", $thousands);
        $table = array(
            900 => "CM",
            500 => "D",
            400 => "CD",
            100 => "C",
            90 => "XC",
            50 => "L",
            40 => "XL",
            10 => "X",
            9 => "IX",
            5 => "V",
            4 => "IV",
            1 => "I"
        );
        while ($value) {
            foreach ($table as $part => $fragment) {
                if ($part <= $value) {
                    break;
                }
            }
            $amount = (int)($value / $part);
            $value -= $part * $amount;
            $result .= str_repeat($fragment, $amount);
        }
        return $result;
    }

    /**
     * Функци для печати перменной
     * @param $value
     */
    public function printValue($value)
    {
        if (!empty($value)) {
            echo "<pre>";
            if (is_array($value) || is_object($value)) {
                print_r($value);
            } else {
                echo $value;
            }
            echo "</pre>\n";
        }
    }

    /**
     * Обрезаем строку до нужной длинны и ставим к обрезку троеточие
     * @param $str - входящая строка. Если строка меньше обрезаемых символов - возвращаем всю строку
     * @param $count - кол-во символов, на которое нужно обрезать строку
     * @param $begin - номер символа, с которого нужно обрезать строку
     * @return string
     */
    function cropStr($str, $qty, $begin = 0)
    {
        $result = "";
        if (!empty($str)) {
            $t_str = htmlspecialchars($str);
            $cur_str_count = strlen($t_str);
            if ($cur_str_count > $qty) {
                $result = mb_substr($t_str, $begin, $qty, 'UTF-8') . "...";
            } else {
                $result = $t_str;
            }
        }
        return $result;
    }

    /**
     * Делает переадресацию при налчии REFERER
     */
    public static function RedirectToReferer()
    {
        if (!empty($_SERVER['HTTP_REFERER'])) {
            header("Location: " . $_SERVER['HTTP_REFERER']);
            die();
        }
    }

    /**
     * @param $url - делате редирект на указанный урл
     */
    public static function RedirectToUrl($url)
    {
        header("Location: " . $url);
        die();
    }

    /**
     * Генериуем случайную строку.
     * @return string
     */
    public static function generateRandomString()
    {
        return md5(uniqid() . mt_rand());
    }

    /**
     * Кодирует данные
     * @param $key - ваш проивзольный ключ
     * @param $data - данные котоыре нужно зашифровать
     * @return string - возаращает строку
     */
    public static function encrypt($key, $data)
    {
        if (function_exists('mcrypt_create_iv')) {
            $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
            $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
            $pub = md5($key . _SECRET_KEY_);
            return base64_encode($iv . openssl_encrypt($data, 'AES-256-OFB', $pub, 0, $iv));
        } else {
            $pub = md5($key . _SECRET_KEY_);
            $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
            $iv = openssl_random_pseudo_bytes($ivlen);
            $ciphertext_raw = openssl_encrypt($data, $cipher, $pub, $options = OPENSSL_RAW_DATA, $iv);
            $hmac = hash_hmac('sha256', $ciphertext_raw, $pub, $as_binary = true);
            $ciphertext = base64_encode($iv . $hmac . $ciphertext_raw);

            return $ciphertext;
        }

    }

    /**
     * Декодирует данные
     * @param $key - ключ
     * @param $data - дата
     * @return string - возвращается строка декодированая
     */
    public static function decrypt($key, $data)
    {
        if (function_exists('mcrypt_create_iv')) {
            $data = base64_decode($data);
            $pub = md5($key . _SECRET_KEY_);
            $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
            $iv = substr($data, 0, $iv_size);

            return $decryptedMessage = @openssl_decrypt(substr($data, $iv_size), 'AES-256-OFB', $pub, 0, $iv);
        } else {
            $pub = md5($key . _SECRET_KEY_);
            $c = base64_decode($data);
            $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
            $iv = substr($c, 0, $ivlen);
            $hmac = substr($c, $ivlen, $sha2len = 32);
            $ciphertext_raw = substr($c, $ivlen + $sha2len);
            $plaintext = openssl_decrypt($ciphertext_raw, $cipher, $pub, $options = OPENSSL_RAW_DATA, $iv);
            $calcmac = hash_hmac('sha256', $ciphertext_raw, $pub, $as_binary = true);
            if (hash_equals($hmac, $calcmac)) {
                return $plaintext;
            }
        }

    }

    /**
     * Генерирует хэш с солью
     * @param $password
     * @return string
     */
    public static function generateHashWithSalt($password)
    {
        $salt = substr(sha1($password), 10, 20) . "\3\1\2\6";
        return sha1(sha1($password) . $salt);
    }

    /**
     * Генерирует уникальную строку UUID
     * @return string
     */
    public static function guidv4()
    {
        if (function_exists('com_create_guid') === true)
            return trim(com_create_guid(), '{}');

        $data = openssl_random_pseudo_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Возвращает, ТРУ - если аякс запрос, и ФАЛС при обычно запросе
     * @return bool
     */
    public static function isAjaxRequest()
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            return true;
        } else {
            return false;
        }
    }

    private static function checkIP($ip)
    {
        $ip = ip2long($ip);
        if ($ip <= 50331647) return false; // '0.0.0.0','2.255.255.255' or false
        $private = array(
            //array(167772160, 184549375),    // '10.0.0.0','10.255.255.255'
            array(2130706432, 2147483647),  // '127.0.0.0','127.255.255.255'
            array(2851995648, 2852061183),  // '169.254.0.0','169.254.255.255'
            array(2886729728, 2887778303),  // '172.16.0.0','172.31.255.255'
            array(3221225984, 3221226239),  // '192.0.2.0','192.0.2.255'
            array(3232235520, 3232301055),  // '192.168.0.0','192.168.255.255'
            array(4294967040, 4294967295)   // '255.255.255.0','255.255.255.255'
        );
        foreach ($private as $p) if ($p[0] <= $ip && $ip <= $p[1]) return false;
        return true;
    }

    public static function getRealUserAddress()
    {
        $keys = [
            'HTTP_X_REAL_IP',
            'HTTP_X_FORWARDED_FOR',
            //'HTTP_X_CLUSTER_CLIENT_IP',
            //'HTTP_FORWARDED_FOR',
            'HTTP_CLIENT_IP'
        ];
        foreach ($keys as $key) {
            if (isset($_SERVER[$key])) {
                $ips = $_SERVER[$key];
                if (strpos($ips, ',')) {
                    $ips = array_map('trim', explode(',', $ips));
                } else {
                    $ips = [$ips];
                }
                foreach ($ips as $ip) {
                    if (self::checkIP($ip)) {
                        return $ip;
                    }
                }
            }
        }
        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    }

    /**
     * @param $filename
     */
    public static function sendCsvHeaderStream($filename)
    {
        if (substr($filename, 0, -3) != 'csv') {
            $filename = $filename . ".csv";
        }
        // disable caching
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");

        // force download
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-type: application/csv; charset = Windows-1251");

        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
    }


    /**
     * @param $filename
     */
    public static function sendTxtHeaderStream($filename)
    {
        if (substr($filename, 0, -3) != 'txt') {
            $filename = $filename . ".txt";
        }
        // disable caching
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");

        // force download
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header("Content-type: application/txt; charset = utf-8");

        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
    }

    public static function array2txtFile($array, $filename)
    {
        if (is_array($array) and count($array) == 0) {
            return null;
        }
        if (is_object($array) and !$array instanceof \PDOStatement ) { return null;}

        self::sendTxtHeaderStream($filename);

        $df = fopen("php://output", 'w');

        /**
         * Если это массив
         */
        if (is_array($array)) {
            foreach ($array as $row) {
                if (!is_string($row)) {
                    $row = implode("",$row);
                }
                fwrite($df, $row."\n");
            }
        }
        /**
         * Если это результат выборки
         */

        if ($array instanceof \PDOStatement){
            while($item = $array->fetch(\PDO::FETCH_ASSOC)){
                foreach ($item as $row) {
                    if (!is_string($row)) {
                        $row = implode("",$row);
                    }
                    fwrite($df, $row."\n");
                }
            }
        }
        fclose($df);

    }



    /**
     * Проверить кончено надо...
     * @param $array принимает или МАССИВ или PDOSTATEMENT
     * @param $titles - поля
     * @param $filename - название файла
     * @return null
     */
    public static function array2csvFile($array, $titles, $filename)
    {
        if (is_array($array) and count($array) == 0) {
            return null;
        }
        if (is_object($array) and !$array instanceof \PDOStatement ) { return null;}

        self::sendCsvHeaderStream($filename);

        $df = fopen("php://output", 'w');
        if ($titles != null) {
            fputcsv($df, $titles, ';');
        }
        /**
         * Если это массив
         */
        if (is_array($array)) {
            foreach ($array as $row) {
                if (!is_array($row)) {
                    $row = (array)$row;
                }
                fputcsv($df, $row, ';');
            }
        }
        /**
         * Если это результат выборки
         */

        if ($array instanceof \PDOStatement){
            while($item = $array->fetch(\PDO::FETCH_ASSOC)){
                foreach ($item as $row) {
                    if (!is_array($row)) {
                        $row = (array)$row;
                    }
                    fputcsv($df, $row, ';');
                }
            }
        }
        fclose($df);

    }

    /**
     * На вход скармливаем файл, на выходе получаем разобранный массив
     * @param $fullFilePath - путь к файлу
     * @param bool $headers - необязательный параметр, заголовки (которые станут ключами массива)
     * @return array
     * @example \core\Tools::csv2Array(__DIR__."/all.csv", \modules\village\models\Village::instance()->factory()->getColumnsName())
     */
    public static function csv2Array($fullFilePath, $headers = false)
    {
        $csvData = file_get_contents($fullFilePath);
        $csvData = str_replace("\r\n", '<br>', $csvData);
        $lines = explode("\n", $csvData);
        foreach ($lines as $kLine => $line) {
            $line = explode(";", $line);
            foreach ($line as $ki => $vi) {
                $line[$ki] = str_replace('#####', "\n", $vi);
            }
            if (!$headers) {
                $array[] = $line;
            } else {
                $time = [];
                foreach ($line as $k => $v) {
                    $time[(!empty($headers[$k])) ? $headers[$k] : ""] = $v;
                }
                $array[] = $time;
            }
        }
        return $array;

    }

}