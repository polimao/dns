<?php

namespace App\Console\Commands;

use App\Console\Commands\Crawler;
use App\Console\Boot;
use App\Http\ZhiHuUser;
use Curl;
class CrawlerZhiHuUser extends Boot{

    protected $signature = 'crawler:zhihuuser {mutix?} {--limit=} {--offset=}';

    /** @var string [描述] */
    protected $description = 'zhihuuser';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->start();

		ZhiHuUser::unguard(true);

        if($this->argument('mutix'))
            $this->mutix();
        else
            $this->grap();

        $this->end();
    }

    public function mutix()
    {
        $count = ZhiHuUser::whereStatus(0)->count();
        $this->scryed($count,8,['artisan','crawler:zhihuuser']);
    }

    public function grap()
    {
        $offset = $this->option('offset');
        $limit = $this->option('limit');

        while(true){
                $query = ZhiHuUser::whereStatus(0)->orderBy('created_at','desc');
                $offset and $query = $query->skip($offset);
                $limit and $query = $query->take($limit);

                $zhihus = $query->get();

                $count = count($zhihus);
                $start_time = time();
        		foreach ($zhihus as $key=>$user) {

                    $craw = new Crawler();
                    $url = $user->url;
                    $this->info($url . "        {$count}/{$key}");

        			$craw->get($url)->startFilter();

                    // $loginNode = $craw->filter('span.name');
                    // if(count($loginNode)){
                    //     $this->error('被防抓取');
                    //     dd($loginNode->text());
                    //     continue;
                    // }
                    $nameNode = $craw->filter('span.name');
                    if(!count($nameNode)){
                        $user->status = -2;
                        $user->save();
                        $this->error('this user is die');
                        continue;
                    }
                    $user->name = $nameNode->text();

                    $cityNode = $craw->filter('span.location');

                    $genderNode = $craw->filter('span.gender i');
                    if(count($genderNode)){
                        if(strstr($genderNode->attr('class'),'female'))
                            $user->gender = 2;
                        else
                            $user->gender = 1;
                        // elseif(strstr($genderNode->attr('class'),'female');
                    }

                    $user->city = count($cityNode)?$cityNode->attr('title'):'';

                    $jobNode = $craw->filter('span.business');

                    $user->job = count($jobNode)?$jobNode->attr('title'):'';

                    $descNode = $craw->filter('span.description');

                    $user->desc = count($descNode)?trim($descNode->text()):'';

                    $user->be_favor = $craw->filter('span.zm-profile-header-user-agree strong')->text();
                    $user->be_thank = $craw->filter('span.zm-profile-header-user-thanks strong')->text();

                    $user->asks = $craw->filter('div.profile-navbar a')->eq(1)->filter('span.num')->text();
                    $user->answers = $craw->filter('div.profile-navbar a')->eq(2)->filter('span.num')->text();


                    $user->concerned = $craw->filter('div.zm-profile-side-following a')->eq(0)->filter('strong')->text();
                    $user->be_concerned = $craw->filter('div.zm-profile-side-following a')->eq(1)->filter('strong')->text();

        			$user->status = 1;

        			$user->save();

                    if((time() - $start_time) % 300 == 0 )
                        sleep(30);
        		}
        }





    }


}

 ?>