<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Console\Boot;
use App\Console\Commands\Crawler;

class zhihu2 extends Boot
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zhihu2';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'zhihu crawler.';

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

        $this->setLogin();

        $url = 'http://www.zhihu.com/people/deng-huai-jin';

        $this->reuse($url);

        $this->end();
    }

    public function reuse($url)
    {
        $crawler = (new Crawler)->get($url)->startfilter();

        $name = $crawler->filter('.title-section.ellipsis span')->text();

        $crawler = (new Crawler)->get($url.'/followees')->startfilter();

        $urls = [];
        dd($crawler->getBody());
        dd($crawler->filter('.zg-link')->text());

        $crawler->filter('.zm-list-content-title a.zg-link')->each(function($node) use (&$urls){
            $urls[] = $node->filter('a.zm-item-link-avatar')->attr('href');
            // dd($node->filter('a.zm-item-link-avatar'));
        });
        dd($url.'/followees',$urls);
    }

    public function setLogin()
    {
        $config = [
                    '__utma' => '51854390.1024287032.1442222313.1442838744.1442838744.1',
                    '__utmb' => '51854390.18.10.1442838744',
                    '__utmc' => '51854390',
                    '__utmt' => '1',
                    '__utmv' => '51854390.100-1|2=registration_date=20130411=1^3=entry_date=20130411=1',
                    '__utmz' => '51854390.1442838744.1.1.utmcsr=zhihu.com|utmccn=(referral)|utmcmd=referral|utmcct=/question/21253022',
                    '_ga'    => 'GA1.2.1024287032.1442222313',
                    '_gat'   => '1',
                    '_xsrf'  => 'ef6f8bc37369a486488bcba53a5ea0a6',
                    '_za'    => '2b78aa32-1db0-45d1-9709-d2fbfdc570c4',
                    'cap_id' => '"ODA4ZDIzMzRkMmIxNGVkMTg2MWU0MGYxMjM5YzM3NDc=|1442839861|67b18bac53505733538e365f45bac8297d311248"',
                    'n_c'    => '1',
                    'q_c1'   => '1783f613456746f7a1f4ae84e67040ff|1441771755000|1441771755000',
                    'z_c0'   => '"QUFBQWVzY2FBQUFYQUFBQVlRSlZUVmFMSjFZbnFrY2RGSzlFZjZINERtRWVMaGV3cjZrajBBPT0=|1442840150|4c337b5b503
                    0236e358fb349503b61fc40282aad"'
                    ];
        foreach ($config as $key => $value) {
            setcookie($key,$value);
        }

    }

}
