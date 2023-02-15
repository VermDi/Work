<?php
/**
 * Created by PhpStorm.
 * User: dulentcov-smishko
 * Date: 21.09.2018
 * Time: 16:27
 */

namespace modules\block;


class Curl
{
    public $agent;
    public $ch = null;
    public $content;
    public $headers;

    public function __construct()
    {
        $this->agent = $this->getRandomAgent();
    }

    /**
     * Убираем куки
     */
    private function clearCookie()
    {
        if (file_exists(__DIR__ . "/cookie.txt")) {
            unlink(__DIR__ . "/cookie.txt");
        }
        return $this;
    }

    /**
     * Новый канал
     */
    public function newChannel()
    {
        curl_close($this->ch);
        $this->clearCookie();
        $this->agent = $this->getRandomAgent();
        $this->ch = curl_init();
        return $this;
    }

    /**
     * Закрывает текущейй соединение
     */
    public function closeChannel()
    {
        curl_close($this->ch);
        $this->ch = null;
    }

    /**
     * Получает содержимое страницы и помещает ее в content
     * @return $this
     */
    public function getUrl($url)
    {
// begin script
        if ($this->ch == null) {
            $this->clearCookie();
            $this->ch = curl_init($url);
        }
// extra headers
        $headers[] = "Accept: */*";
        $headers[] = "Connection: Keep-Alive";
// basic curl options for all requests
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->ch, CURLOPT_USERAGENT, $this->agent);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($this->ch, CURLOPT_COOKIEFILE, __DIR__ . "/cookie.txt");
        curl_setopt($this->ch, CURLOPT_COOKIEJAR, __DIR__ . "/cookie.txt");
        $this->content = curl_exec($this->ch);
        return $this;
    }

    /**
     * Получает имя файла со списком агентов, и возвращает рэндомного агента
     * @param string $fileName
     * @return string
     */
    public function getRandomAgent($fileName = __DIR__ . "/agent.txt")
    {
        $handel = fopen($fileName, 'r+');
        if (!$handel)
            return '';
        $cLineCount = 0;
        $sLineOut = '';
        while (!feof($handel)) {
            $cLineCount++;
            $sLine = fgets($handel);
            if (rand(1, $cLineCount) == $cLineCount) {
                $sLineOut = $sLine;
            }
        }
        return $sLineOut;

    }
}