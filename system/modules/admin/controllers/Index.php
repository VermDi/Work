<?php namespace modules\admin\controllers;

use core\Controller;
use core\Errors;
use core\Headers;
use core\Html;
use core\Request;
use modules\admin\models\ErrorsLog;

class Index extends Controller
{
	public function actionIndex($p = false)
	{
		if ($p) {
			Errors::e404();
		}
//    	throw new \Exception("adasdasd");

		Html::instance()->content = $this->render('/dashboard.php');
		Html::instance()->renderTemplate("@admin")->show();
	}

    public function getCleareroors()
    {
        ErrorsLog::instance()->truncate();
        self::goBack();
    }

    public function actionRobots()
    {
        $robots_file = $_SERVER['DOCUMENT_ROOT'] . "/robots.txt";
        if (isset($_POST['submit'])) {
            $name = $_POST['text'];
            ob_start();
            echo $name;
            $menu = ob_get_contents();
            ob_end_clean();
            if (!$fp = @fopen($robots_file, "w", _FILE_R_)) {
                die ("НЕ УДАЕТСЯ СОЗДАТЬ ФАЙЛ");
            } else {
                @fwrite($fp, html_entity_decode($menu));
                @fclose($fp);
                header("Location: /admin/robots");
            }
        }
        Html::instance()->content = $this->render('/robots.php');
        Html::instance()->renderTemplate("@admin")->show();
    }

    public function postConsole()
    {
        putenv('LANG=en_US.UTF-8');
        $mes = Request::instance()->get('message');
        $res = shell_exec($mes);
        if (mb_detect_encoding($res, 'auto')) {
            $res = mb_convert_encoding($res, mb_internal_encoding(), mb_detect_encoding($res, 'auto'));
        }

        echo "<pre>";
        echo strip_tags($res);
        die();
    }

    public function actionMetrics()
    {
        $file = $_SERVER['DOCUMENT_ROOT'] . "/metrics.txt";

        if (!empty($_POST['metrica'])){
            file_put_contents($file, Request::instance()->get('metrica'));
        }
        if (file_exists($file)) {
            $content = file_get_contents($file);

        } else {
            $content = " ";
        }
        Html::instance()->content = $this->render('metrica.php', $content);
        Html::instance()->renderTemplate("@admin")->show();


    }

    /**
     * Экшен который сканирует файлы на подозрительный код, как вирусный.
     */
    public function actionAntivirus()
    {
        $Directory = new \RecursiveDirectoryIterator(realpath($_SERVER['DOCUMENT_ROOT'] . "/../"));
        $Iterator = new \RecursiveIteratorIterator($Directory);
        $Regex = new \RegexIterator($Iterator, '/^.+\.php$/i', \RecursiveRegexIterator::GET_MATCH);
        if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'result')) {
            @unlink(__DIR__ . DIRECTORY_SEPARATOR . 'result');
        }
        $count = 0;
        $badCodes = ['eval(', 'eval ', '$GLOBALS[\'', '\x5a\x2c\x7a'];
        foreach ($Regex as $file) {
            $content = file_get_contents($file[0]);
            foreach ($badCodes as $code) {
                if ($pos = strpos($content, $code)) {
                    file_put_contents(
                        __DIR__ . DIRECTORY_SEPARATOR . 'result', "<div>" . $file[0] .
                        "<br>Позиция: <span style='color:red;'>" . $pos . "</span><blockquote><code>" .
                        htmlspecialchars_decode(substr($content, $pos - 200, 400))
                        . "</code></blockquote> </div>\r\n",
                        FILE_APPEND);
                    $count++;
                    break;
                }
            }
        }

        echo "Эти $count файлы содержат код, который может быть похож на вирусный!\r\n<hr>";
        echo nl2br(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'result'));
        ?>
        <style>
            blockquote {
                display: none;
            }

            div:hover {
                background-color: lightgrey;
            }

            div:hover blockquote {
                padding: 5px;
                display: block;
                position: fixed;
                width: 50%;
                top: 0px;
                right: 0px;
                background-color: white;
                border: 1px solid black;
            }
        </style>
        <?
    }
}