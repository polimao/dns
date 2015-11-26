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
    		$query = ZhiHu::where('status',0);
            $offset and $query = $query->skip($offset);
            $limit and $query = $query->take($limit);

            $zhihus = $query->get();
    		foreach ($zhihus as $zhihu) {

                $craw = new Crawler();
                $url = $zhihu->url;
                $this->info($url);

    			$craw->get($url)->startFilter();

                $zhihu->title = trim($craw->filter('h2.zm-item-title')->text());

                $zhihu->content = $craw->filter('div.zm-editable-content')->text();

                $answerNode = $craw->filter('h3#zh-question-answer-num');

                $zhihu->answer_num = count($answerNode)?$answerNode->attr('data-num'):0;

                $concerned_num = $craw->filter('div#zh-question-side-header-wrap')->text();

                preg_match('/\d+/', $concerned_num,$matchs);

                $zhihu->concerned_num = isset($matchs[0])?$matchs[0]:0;

                // $viewNode = $craw->filter('.zm-side-section-inner .zg-gray-normal')->last()->text();

// dd($viewNode);
                // $zhihu->views = count($viewNode)?$viewNode->text():0;
                // dd($zhihu->views);

                $zhihu->status = 1;

                $zhihu->save();

                $this->comment('answer----conserned:   ' . $zhihu->answer_num.'---'.$zhihu->concerned_num);

				$links = $craw->filter('a.question_link');
                if(count($links))
                $links->each(function($node){
					$link = 'http://www.zhihu.com' . $node->attr('href');
                    if(!ZhiHu::where('url',$link)->first()){
                        $this->question($link);
    					ZhiHu::saveData(['url'=>$link]);
                    }
				});

                $userLinks = $craw->filter('a.author-link');
                if(count($userLinks))
                $userLinks->each(function($node){
                    $link = 'http://www.zhihu.com' . $node->attr('href');
                    ZhiHuUser::firstOrCreate(['url'=>$link],['url'=>$link]);
                });

    		}
    	}

    }


}

 ?>