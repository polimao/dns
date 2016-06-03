<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

use Curl;
use App\Http\JiaYuan;

class JiaYuanCrawler extends Command {

    protected $signature = 'jiayuan';

    /** @var string [描述] */
    protected $description = '世纪佳缘';

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
    	$num = 1;
    	while (true) {
	    	$url = "http://search.jiayuan.com/v2/search_v2.php";
    		$curl = new Curl();

    		$param = [
                "p" => $num,
                "sn" => "default",
                "stc" => "11:4"
            ];

            $res = $curl->post($url, $param);

            $num++;
    		$this->comment('page : ' . $num);

    		$data = json_decode($res->body,true);

            $data = $data['userInfo'];
            foreach ($data as $key => $jiayuan) {
                $jiayuan = array_filter($jiayuan);

                if(!JiaYuan::find($jiayuan['uid'])){
                    $this->info($jiayuan['uid'] . '添加成功');

                    $jiayuan['id'] = $jiayuan['uid'];
                    JiaYuan::saveData($jiayuan);
                }else
                     $this->error($jiayuan['uid'] . '   aready exist');
            }

            sleep(0.4);
    	}
        // foreach ($arr as $key => $value) {
        //     // $this->info($value);
        //     $url = "http://www.pullword.com/process.php";
        //     $curl = new Curl();
        //     $param = array(
        //         "param1" => "0",
        //         "param2" => "0",
        //         "source"    => $value,
        //         );
        //     $this->info('剩余------------------------'. (count($arr)-$key).'组');
        //     $res = $curl->post($url, $param);
        //     $words = explode("\n",$res);
        //     $Py = new ChineseSpell();
        //     foreach ($words as $word) {
        //         $word = trim($word);
        //         // $word_clone = iconv("UTF-8", "GB2312//IGNORE", $word);
        //         $word_clone = mb_convert_encoding($word, "gb2312", "UTF-8");
        //         $pinyin = $Py->getFullSpell($word_clone);

        //         // $this->info($word . '   --  ' . $pinyin);

        //         if($domain = Domain::where('word',$word)->first())
        //         {
        //             $domain->entry_cnt = $domain->entry_cnt + 1;
        //             $domain->save();
        //         }
        //         else{
        //             Domain::unguard(true);
        //             Domain::create([
        //                 'word'       => $word,
        //                 'word_len'   => mb_strlen($word),
        //                 'pinyin'     => trim($pinyin),
        //                 'pinyin_len' => strlen($pinyin),
        //                 ]);
        //         }
        //     }
        // }
    }

}
