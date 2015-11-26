<?php

namespace App\Console\Commands;

use App\Console\Commands\Crawler;
use App\Console\Boot;
use App\Http\ZhiHuUser;
use Curl;
class CrawlerZhiHuUser extends Boot{

    protected $signature = 'crawler:zhihuuser {mutix?}';

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

		$this->grap();

        $this->end();
    }

    public function grap()
    {

    	while (true) {
    		$zhihus = ZhiHuUser::whereStatus(0)->orderBy('created_at','desc')->limit(100)->get();
    		foreach ($zhihus as $user) {

                $craw = new Crawler();
                $url = $user->url;
                $this->info($url);

    			$craw->get($url)->startFilter();

                // $loginNode = $craw->filter('span.name');
                // if(count($loginNode)){
                //     $this->error('被防抓取');
                //     dd($loginNode->text());
                //     continue;
                // }

                $nameNode = $craw->filter('span.name');
                if(!count($nameNode)){
                    $user->status = -1;
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



				// ZhiHu::saveData(compact('url','status','title'));


				// $craw->filter('a.question_link')->each(function($node){
				// 	$link = $node->attr('href');
				// 	$child_url = 'http://www.zhihu.com'.$link;
				// 	if(!ZhiHu::where('url',$child_url)->first())
				// 		ZhiHu::saveData(['url'=>$child_url]);
				// });
    		}
    	}





    }


}

 ?>