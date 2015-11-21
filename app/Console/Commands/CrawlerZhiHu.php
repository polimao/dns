<?php 

namespace App\Console\Commands;

use App\Console\Commands\Crawler;
use App\Console\Boot;
use App\Http\ZhiHu;
use App\Http\ZhiHuUser;
use Curl;
class CrawlerZhiHu extends Boot{

    protected $signature = 'crawler:zhihu {mutix?}';

    /** @var string [描述] */
    protected $description = 'zhihu';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->start();

		ZhiHu::unguard(true);

		$this->grap();

        $this->end();
    }

    public function grap()
    {
    	while (true) {
    		$zhihus = ZhiHu::whereStatus(0)->limit(100)->get();
    		foreach ($zhihus as $zhihu) {

                $craw = new Crawler();
                $url = $zhihu->url;
                $this->info($url);

    			$craw->get($url)->startFilter();

                $zhihu->title = $craw->filter('h2.zm-item-title')->text();

                $answerNode = $craw->filter('h3#zh-question-answer-num');

                $zhihu->answer_num = count($answerNode)?$answerNode->attr('data-num'):0;

                $concerned_num = $craw->filter('div#zh-question-side-header-wrap')->text();

                preg_match('/\d+/', $concerned_num,$matchs);
                $this->comment('answer----conserned:   ' . $zhihu->answer_num.'---'.$matchs[0]);

                $zhihu->concerned_num = $matchs[0];

				$zhihu->status = 1;

				$zhihu->save();



				// ZhiHu::saveData(compact('url','status','title'));


				$craw->filter('a.question_link')->each(function($node){
					$link = $node->attr('href');
					$child_url = 'http://www.zhihu.com'.$link;
					if(!ZhiHu::where('url',$child_url)->first())
						ZhiHu::saveData(['url'=>$child_url]);
				});

                $craw->filter('a.author-link')->each(function($node){
                    $link = $node->attr('href');
                    $child_url = 'http://www.zhihu.com'.$link;
                    if(!ZhiHuUser::where('url',$child_url)->first())
                        ZhiHuUser::saveData(['url'=>$child_url]);
                });

                
    		}
    	}
    	




    }


}

 ?>