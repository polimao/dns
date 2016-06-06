<?php
namespace App\Console\Commands;

use App\Console\Boot;
use Curl;
use App\Http\JiaYuan;

class JiaYuanCrawler extends Boot {

    protected $signature = 'jiayuan {argument?}';

    /** @var string [描述] */
    protected $description = '世纪佳缘';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->argumentRun();
    }

    /**
     * [grab 抓取]
     * @author limao 2015-07-25
     * @return [type] [description]
     */
    public function getList()
    {
    	$num = 1;
    	while (true) {
	    	$url = "http://search.jiayuan.com/v2/search_v2.php";
    		$curl = new Curl();

    		$param = [
                "p" => $num,
                // "sn" => "default",
                "stc" => "28:8"
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
        
    }

    /**
     *  获取世纪佳缘资料 或者可以这么去获得用户详细数据
     * @return [type]
     */
    public function getLists()
    {
        $num = 1;
        $str = '';
        while ($num++) {
            $this->comment($num);
             $url = 'http://www.jiayuan.com/msg/draft.php?tuijian=1&tuijian_type=hello&to_uid=47614256&filter=1';

            $str .= $this->get($url);

            // preg_match_all('/uid_disp="(\d*)"/', $str, $matches);
            // foreach ($matches[1] as $key => $value) {
            //     JiaYuan::firstOrCreate(['source_id'=>$value]);
            //     $this->info($value);
            // }

            if($num >= 200 ) break;
        }
        file_put_contents('./jiayuan.html', $str);
       
    }


    public function headerCurl($url) {


        $headers = [
'Host: www.jiayuan.com', 'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:48.0) Gecko/20100101 Firefox/48.0', 'Accept: application/json, text/javascript, */*', 'Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3', 'Accept-Encoding: gzip, deflate', 'X-Requested-With: XMLHttpRequest', 'Referer: http://www.jiayuan.com/', 'Cookie: jy_index_total=hi; save_jy_login_name=710898853%40qq.com; stadate1=34804887; myloc=11%7C1105; myage=25; mysex=m; myuid=34804887; myincome=30; upt=z6mXSKr41qAxlOJnzOyvwLBcUTZbGRzOUPTRlKZJYoeaPS%2A9xsQ3tJEKVljPiUMvugTJCb4WgZn6snvw-VoWdYJGea0U; ip_loc=11; view_m=1; photo_scyd_35804887=yes; PHPSESSID=aebab7ff8ff2f74de77b6e2fd99c8c70; SESSION_HASH=4738771f82a8fedcdd8b14a9df73ba075b4660af; user_access=1; REG_REF_URL=http://www.jiayuan.com/; PROFILE=35804887%3A%25E5%25B0%258F%25E7%2588%25B5%25E7%2588%25B7%3Am%3Aimages1.jyimg.com%2Fw4%2Fglobal%2Fi%3A0%3A%3A0%3Azwzp_m.jpg%3A1%3A1%3A50%3A0; RAW_HASH=lnPWpMw45Q14WMHXfNOb4JSmu65JyE1hiA14cD9EnfBQBBFwcVu44gZsQR0Ffg4PB5BE6PudBWRZo-lRJkpoJQUa5BVjdq2UyOQnAgD5zj6twco.; COMMON_HASH=ed24b1982e87a61ad0791076a06506f5; sl_jumper=%26cou%3D17%26omsg%3D0%26dia%3D0%26lst%3D2016-05-28; last_login_time=1464802480; main_search:35804887=%7C%7C%7C00; date_pop_35804887=1; pclog=%7B%2235804887%22%3A%221464802493740%7C1%7C0%22%7D; IM_S=%7B%22IM_CID%22%3A8780806%2C%22svc%22%3A%7B%22code%22%3A0%2C%22nps%22%3A0%2C%22unread_count%22%3A%221%22%2C%22ocu%22%3A0%2C%22ppc%22%3A0%2C%22jpc%22%3A0%2C%22regt%22%3A%221282973304%22%2C%22using%22%3A%22%22%2C%22user_type%22%3A%220%22%2C%22uid%22%3A35804887%7D%2C%22m%22%3A1%2C%22f%22%3A0%2C%22omc%22%3A0%7D; IM_CS=2; IZ_bind35804887=0; IM_ID=8; IM_TK=1464802977098; IM_M=%5B%5D; IM_CON=%7B%22IM_TM%22%3A1464802494757%2C%22IM_SN%22%3A1%7D; pop_time=1464802883790'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        // curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie); //读取cookie
        $rs = curl_exec($ch); //执行cURL抓取页面内容
        curl_close($ch);
        return $rs;
    }


}
