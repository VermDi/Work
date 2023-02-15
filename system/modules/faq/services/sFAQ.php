<?

namespace modules\faq\services;

use Exception;
use PDO;
use PDOException;

class sFAQ
{
    protected static $instance;

    public static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    public function getHomeUri(){
        $protokol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $HomeUri = $protokol.$_SERVER['HTTP_HOST'];
        return $HomeUri;
    }
}
