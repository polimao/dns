<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Console\Commands\Crawler;
use Curl;
// use App\Http\JiaYuan;

class SiJiaoMaoCrawler extends Command {

    protected $signature = 'sijiaomao';

    /** @var string [描述] */
    protected $description = '四脚猫每日一题';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('--------start-----');
        $this->grab();

        $this->info('------end---------');
    }

    /**
     * [grab 抓取]
     * @author limao 2015-07-25
     * @return [type] [description]
     */
    public function grab()
    {
    	$page = 87;
        $html = '';
        $fp = fopen('./sijiaomao.html', 'w');
    	while (true) {
	    	$url = "http://blog.sijiaomao.com/?p=" . $page;

            $craw = new Crawler();

            $this->comment($url);

            $craw->get($url)->startFilter();

            $title_node = $craw->filter('article h1');

            $title = $title_node->count()?$title_node->text():'';
            $this->info($title);
            if(!$title)
                $this->error('  页面不存在');
            if(mb_substr(trim($title), 0,7) == '四脚猫每日一题')
            {
                $this->info('           获取一题');
                $content = '<article>' . $craw->filter('article')->html() . '</article';
                fwrite($fp, $content);
            }

            if($page == 1553)
                break;
            $page++;
    	}

    }

}
