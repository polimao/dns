<?php

namespace App\Console\Commands;

use App\Console\Commands\Crawler;
use App\Console\Boot;
use App\Http\ZhiHu;
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

    			$craw->get($url)->startFilter();

                $titleNode = $craw->filter('h2.zm-item-title');

				$zhihu->title = $titleNode->text();

				$zhihu->status = 1;

				$zhihu->save();

				$this->info($url);

				// ZhiHu::saveData(compact('url','status','title'));


				$links = $craw->filter('a.question_link');
                if(count($links))
                $links->each(function($node){
					$link = $node->attr('href');
					$child_url = 'http://www.zhihu.com'.$link;
					if(!ZhiHu::where('url',$child_url)->first())
						ZhiHu::saveData(['url'=>$child_url]);
				});
    		}
    	}





    }


}

 ?>