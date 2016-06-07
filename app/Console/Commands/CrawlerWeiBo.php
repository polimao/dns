<?php

namespace App\Console\Commands;

use App\Console\Boot;
use App\Console\Commands\Crawler;
use App\Http\WeiBo;

class CrawlerWeiBo extends Boot
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weibo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "weibo";


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->start();
        
        $this->grab();

        $this->end();
    }

    public function grab()
    {
        $url = 'http://weibo.com/u/1670458304';
        $result = $this->cacertCurl($url);
        dd($result);
        // $weibos = WeiBo::whereStatus(0)->get();

        // $count = count($weibos);

        // foreach ($weibos as $key => $weibo) {
        //     $this->comment($weibo->url . "      $key/$count");

        //     $crawler = new Crawler;


        //     $crawler->get($weibo->url)->startfilter();

        //     $file = fopen('./test.html', 'w');
        //     fwrite($file, $crawler->getBody());
        //     $weibo->name = $crawler->filter('h1.username')->text();

        //     $weibo->save();
        // }
    }

    public function cacertCurl($url)
    {
        $ch = curl_init();
        // Add following two lines
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, TRUE); 
        curl_setopt ($ch, CURLOPT_CAINFO, storage_path("/cacert.pem"));
        // End
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mandrill-PHP/1.0.54');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 600);

        curl_setopt($ch, CURLOPT_URL, $url);

        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

}
