<?php

namespace App\Console\Commands;

use App\Console\Commands\Crawler;
use App\Console\Boot;
use App\Http\ZhiHu;
use App\Http\ZhiHuUser;
use Curl;
class CrawlerZhiHu extends Boot{

    protected $signature = 'crawler:zhihu {mutix?} {--limit=} {--offset=}';

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

		if($this->argument('mutix'))
            $this->mutix();
        else
            $this->grap();

        $this->end();
    }

    public function mutix()
    {
        $count = ZhiHu::whereStatus(0)->count();
        $this->scryed($count,10,['artisan','crawler:zhihu']);
    }

    public function grap()
    {

        $offset = $this->option('offset');
        $limit = $this->option('limit');

    	while (true) {
    		$query = ZhiHu::whereStatus(0);
            $offset && $query = $query->skip($offset);
            $limit && $query = $query->take($limit);

            $zhihus = $query->get();
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


				$links = $craw->filter('a.question_link');
                if(count($links))
                $links->each(function($node){
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