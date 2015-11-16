<?php

namespace App\Console\Commands;

use Symfony\Component\DomCrawler\Crawler as Crw;
use Curl;

class Crawler extends Crw
{

    protected $curl = null;
    protected $body = null;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($node = null, $currentUri = null, $baseHref = null)
    {
        parent::__construct($node, $currentUri, $baseHref);

        $this->curl = new Curl();
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function get($url, $callback=null)
    {
        $this->body = $this->curl->get($url);

        // $this->body = iconv('gbk','utf-8//IGNORE', $res->body);
        // $this->body = mb_convert_encoding($res->body, 'gbk', 'utf-8');

        if(is_object($callback)){
            $this->body = $callback($this->body);
        };
        return $this;
    }

    public function curl_get($url, $cookie) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie); //读取cookie
        $rs = curl_exec($ch); //执行cURL抓取页面内容
        curl_close($ch);
        return $rs;
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function post($url, $callback=null)
    {
        $this->body = $curl->post($url);

        // $this->body = iconv('gbk','utf-8', $res->body);

        if(is_object($callback)){
            $this->body = $callback($this->body, $this);
        };
        return $this;
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function getBody()
    {
        return $this->body;
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function startFilter()
    {
        $this->addHtmlContent($this->body);
        return $this;
    }
}
