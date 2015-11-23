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
        $weibos = WeiBo::whereStatus(0)->get();

        $count = count($weibos);

        foreach ($weibos as $key => $weibo) {
            $this->comment($weibo->url . "      $key/$count");

            $crawler = new Crawler;


            $crawler->get($weibo->url)->startfilter();

            $file = fopen('./test.html', 'w');
            fwrite($file, $crawler->getBody());
            $weibo->name = $crawler->filter('h1.username')->text();

            $weibo->save();
        }
    }

}
