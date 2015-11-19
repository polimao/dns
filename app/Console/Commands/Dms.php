<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Console\ChineseSpell;
use Curl;
use App\Http\Domain;

class Dms extends Command {

    protected $signature = 'dms:make';

    /** @var string [描述] */
    protected $description = '抓取可用的域名';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('--------start-----');
        $this->grab();
        // $this->validation();
        $this->info('------end---------');
    }

    /**
     * [grab 抓取]
     * @author limao 2015-07-25
     * @return [type] [description]
     */
    public function grab()
    {
        $text = file_get_contents(storage_path('book.txt'));
        $arr = $this->str_split_unicode($text,1000);
        // $arr = array_reverse($arr);
        foreach ($arr as $key => $value) {
            // $this->info($value);
            $url = "http://www.pullword.com/process.php";
            $curl = new Curl();
            $param = array(
                "param1" => "0",
                "param2" => "0",
                "source"    => $value,
                );
            $this->info('剩余------------------------'. (count($arr)-$key).'组');
            $res = $curl->post($url, $param);
            $words = explode("\n",$res);
            $Py = new ChineseSpell();
            foreach ($words as $word) {
                $word = trim($word);
                // $word_clone = iconv("UTF-8", "GB2312//IGNORE", $word);
                $word_clone = mb_convert_encoding($word, "gb2312", "UTF-8");
                $pinyin = $Py->getFullSpell($word_clone);

                // $this->info($word . '   --  ' . $pinyin);

                if($domain = Domain::where('word',$word)->first())
                {
                    $domain->entry_cnt = $domain->entry_cnt + 1;
                    $domain->save();
                }
                else{
                    Domain::unguard(true);
                    Domain::create([
                        'word'       => $word,
                        'word_len'   => mb_strlen($word),
                        'pinyin'     => trim($pinyin),
                        'pinyin_len' => strlen($pinyin),
                        ]);
                }
            }
        }
    }

    /**
     * [validation 验证域名可用]
     * @author limao 2015-07-25
     * @return [type] [description]
     */
    public function validation()
    {
        while (true) {
            $domains = Domain::where('status',0)->limit(1000)->orderBy('id','desc')->get();
            if(!count($domains))
                return;
            $curl = new Curl;
            $cnt = count($domains);
            foreach ($domains as $key => $domain) {
                $url = "http://www.yumingco.com/api";
                $res = $curl->get($url, ['domain' => $domain->pinyin,'suffix'=>'com']);
                $res = json_decode($res,true);
                $domain->available = (int)$res['available'];
                $domain->query_status = (int)$res['status'];
                $domain->status = 1;
                $domain->save();
                $this->info($domain->word.'     |   ' . $domain->available.'    剩余：'.($cnt-$key));
            }
        }
    }

    public function validation2()
    {
        while (true) {
            $domains = Domain::where('status',0)->limit(1000)->get();
            if(!count($domains))
                return;
                // header("Content-type: text/html; charset=utf-8");
            foreach ($domains as $domain) {
                $url = "http://www.aaw8.com/Api/DomainApi.aspx";
                $curl = new Curl;
                $str = $curl->get($url, ['domain' => $domain->pinyin . '.com']);
                $start = strpos($str,"{");
                $end = strpos($str,'}');
                $res = substr($str,$start,$end-$start+1);
                $res = json_decode($res,true);
                echo $domain->id.'                                         '.$res['Result'];
                // $res = json_decode($res,true);
                // $this->info($res->body);
                if($res['StateID'] == 210 || $res['StateID'] == 211){

                    $domain->available = (int)($res['StateID'] == 210);
                    $domain->query_status = 1;
                    $domain->status = 1;
                    $domain->save();
                }
            }
        }
    }

    /**
     * 将unicode字符串按传入长度分割成数组
     * @param  string  $str 传入字符串
     * @param  integer $l   字符串长度
     * @return mixed      数组或false
     */
    public function str_split_unicode($str, $l = 0) {
        if ($l > 0) {
            $ret = array();
            $len = mb_strlen($str, "UTF-8");
            for ($i = 0; $i < $len; $i += $l) {
                $ret[] = mb_substr($str, $i, $l, "UTF-8");
            }
            return $ret;
        }
        return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
     }
}
