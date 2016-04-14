<?php

namespace App\Console\Commands;

use App\Console\Boot;
use App\Console\Commands\Crawler;
use App\Http\LaGou;

class CrawlerLaGou extends Boot
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lagou';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "lagou";


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

        $url = 'http://www.lagou.com/jobs/positionAjax.json?city=%E5%8C%97%E4%BA%AC';  //调用接口的平台服务地址

        $pn = 1;
        while (true) {

            $post_string = array('first'=>false,'kd'=>'PHP','pn'=>$pn);

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            $data = curl_exec($ch);
            curl_close($ch);
    // dd($data);
            $data = json_decode($data,1);

            foreach ($data['content']['result'] as $key => $value) {
                $salary = explode('-',$value['salary']);

                $value['min_salary'] = (int)$salary[0];
                $value['max_salary'] = (int)end($salary);

                // 15k-20k


                LaGou::saveData($value);
                $this->info($value['salary'] . '     ' . $value['companyName']);
            }
            $pn++;
        }
        $this->end();
    }
}
