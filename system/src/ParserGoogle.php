<?php
/*
на входе - Ключ и домен!!!
На выходе список сайтов в топ 100 по этому запросу (ссылку) и позиция домена который был на входе
 */

use Curl\Curl;



class ParserGoogle
{
    #const URL_CLASS_URL = 'http://crm.e-mind.ru/class-url/class-url.php'; // ссылка с внешним скриптом-прокси
    #const URL_CLASS_URL = 'http://test1.ru/class-url/class-url.php';
    #const URL_KEY = 'key8427836816904058'; // ключ доступа

    private $curl;
    private $domain;
    private $keyword;
    private $data = [];
    private $proxy_url;
    private $proxy_pass;
    public $error = [];


    function __construct()
    {
        $this->curl_init();
    }


    private function curl_init()
    {
        $userAgents = array(
            0 => 'Mozilla/5.0 (Windows NT 6.3; rv:36.0) Gecko/20100101 Firefox/36.0',
            1 => 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36',
            2 => 'Mozilla/5.0 (compatible, MSIE 11, Windows NT 6.3; Trident/7.0;  rv:11.0) like Gecko',
            3 => 'Mozilla/5.0 (Windows NT 6.1; rv:49.0) Gecko/20100101 Firefox/49.0',
            4 => 'Mozilla/5.0 (Windows NT 6.1; Trident/7.0; rv:11.0) like Gecko'
        );

        $this->curl = new Curl();
        $this->curl->setUserAgent($userAgents[rand(0,count($userAgents)-1)]);
        $this->curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $this->curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
        $this->curl->setOpt(CURLOPT_FOLLOWLOCATION, 1);
        $this->curl->setOpt(CURLOPT_MAXREDIRS, 10);
        $this->curl->setOpt(CURLOPT_AUTOREFERER, 1);
        $this->curl->setOpt(CURLOPT_TIMEOUT, 100);        // количество секунд для выполнения cURL-функций
        $this->curl->setOpt(CURLOPT_CONNECTTIMEOUT, 100);
    }


    public function setDomain($domain) {
        $this->domain = $domain;
    }

    public function setKeyword($keyword) {
        $this->keyword = $keyword;
    }

    public function setProxy($proxy) {
        $this->proxy_url = $proxy['url'];
        $this->proxy_pass = $proxy['pass'];
    }

    public function getData()
    {
        return $this->data;
    }


    public function parse()
    {
        $this->request();
    }


    private function request()
    {
        $http_url = 'https://www.google.ru/search';
        $http_url_params = [
            'q' => $this->keyword,
            'num' => 100,
            #'start' => $start,
        ];

        $curl_data = array(
            'key' => $this->proxy_pass,
            'http_url' => $this->buildURL($http_url, $http_url_params),
            'http_method' => 'GET',
        );

        // HTTP POST на наш прокси скрипт
        $this->curl->post($this->proxy_url, $curl_data);
        #print_r($this->curl->responseHeaders);

        if ($this->curl->error) {
            $this->error['class'] = get_class($this);
            $this->error['errorCode'] = $this->curl->errorCode;
            $this->error['errorMessage'] = $this->curl->errorMessage;
        }
        else {
            if ($result_array = json_decode($this->curl->response, true)) {
                if ($result_array['error'] == 1) { // ошибка в json ответе
                    $this->error['class'] = 'Url';
                    $this->error['errorCode'] = $result_array['errorCode'];
                    $this->error['errorMessage'] = $result_array['errorMessage'];
                }
                else {
                    $this->parsePage( base64_decode($result_array['response']) );
                }
            } else {
                $this->error['json'] = 'Bad JSON';
                $this->error['json_string'] = $this->curl->response;
            }
        }
    }


    private function buildURL($url, $params = array())
    {
        return $url . (empty($params) ? '' : '?' . http_build_query($params, '', '&'));
    }


    // Ищем данные в html
    private function parsePage($html)
    {
        preg_match_all('#<div class="g">.+?<h3 class="r"><a href="([^\"]+)".+?</div>#is', $html, $matches);
        if (count($matches[1]) == 0) {
            $this->error['parse_matches'] = "count(matches[1]) = 0";
            return;
        }

        $this->data['all_result'] = [];

        for($i=0; $i<count($matches[1]); $i++) {
            $url = $matches[1][$i];
            $this->data['all_result'][$i+1] = $url;
        }

        foreach ($this->data['all_result'] as $key=>$value) {
            if ( preg_match("#".$this->domain."#is", $value) ) {
                $this->data['find_domain_position'] = $key;
                break;
            } else {
                $this->data['find_domain_position'] = 0;
            }
        }

        $this->data['domain'] = $this->domain;
        $this->data['key'] = $this->keyword;
    }

}