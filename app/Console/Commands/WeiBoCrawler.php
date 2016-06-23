<?php

namespace App\Console\Commands;

use App\Console\Boot;
use Symfony\Component\DomCrawler\Crawler;
use App\Http\WeiBo;

class WeiBoCrawler extends Boot
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weibo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "weibo";


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->start();

        foreach (range(549,20000) as $page) {
            $this->grab($page);
        }

        $this->end();
    }

    public function grab($page)
    {
        $url = "http://www.weibo.com/u/1670458304?is_search=0&visible=0&is_all=1&is_tag=0&profile_ftype=1&page=" . $page . "#feedtop";

        $this->comment(PHP_EOL . 'page->' . $page . PHP_EOL);
        $result = $this->cacertCurl($url);



        $preg = '/FM\.view\((\{"ns":"pl\.content\.homeFeed\.index","domid":"Pl_Official[\s\S]*?)\)<\/script>/';

        preg_match_all($preg,$result,$matchs);

        $data = json_decode($matchs[1][0],true);

        // $data = "ns":"","domid":"","css":"","js":"","html":""
        $mids = [];

        $fh = fopen('./mashier.html', "a");

        $crawler = new Crawler;
        $crawler->addHtmlContent($data['html']);

        $crawler->filter('.WB_cardwrap')->each(function($node) use($mids,$fh){
            $mids[] = $mid = $node->attr('mid');
            $this->info('mid : ' . $mid);
            if(WeiBo::where('mid',$mid)->exists()){
                $this->comment('    exists');
                return ;
            }
            $face_node = $node->filter('.WB_face div.face a.W_face_radius');

            if(!$face_node->count())
                return ;
            $name = $face_node->attr('title');

            $html = $node->html();

            $content = $node->filter('.WB_text')->text();

            $like_num = (int)$node->filter('.WB_feed_handle li')->eq(1)->filter('em')->eq(1)->text();

            $comment_num = (int)$node->filter('.WB_feed_handle li')->eq(2)->filter('em')->eq(1)->text();

            $forward_num = (int)$node->filter('.WB_feed_handle li')->eq(0)->filter('em')->eq(1)->text();

            $original_time = $node->filter('.WB_from a')->attr('title');

// var_dump(compact('mid','name','content','like_num','comment_num','forward_num','original_time'));

            WeiBo::saveData(compact('mid','name','html','content','like_num','comment_num','forward_num','original_time'));

            // fwrite($fh, $node->html());
            // dd($node->html());
        });
        // dd();
        // dd($crawler->filter('body')->text());

        // $result = iconv('gbk','utf-8//IGNORE', $result);

        // $weibos = WeiBo::whereStatus(0)->get();

        // $count = count($weibos);

        // foreach ($weibos as $key => $weibo) {
        //     $this->comment($weibo->url . "      $key/$count");

        //     $crawler = new Crawler;


        //     $crawler->get($weibo->url)->startfilter();

        //     $file = fopen('./test.html', 'w');
        //     fwrite($file, $crawler->getBody());
        //     $weibo->name = $crawler->filter('h1.username')->text();

        //     $weibo->save();
        // }
    }

    public function cacertCurl($url)
    {
        $ch = curl_init();
        // Add following two lines
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
        curl_setopt ($ch, CURLOPT_CAINFO, storage_path("/cacert.pem"));
        // End
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mandrill-PHP/1.0.54');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 600);
        curl_setopt($ch, CURLOPT_URL, $url);




            $headers = [
"Cookie: SINAGLOBAL=2825537780073.9014.1463130486655; SUB=_2A256OE-pDeTxGedH7FUV9SzOzTuIHXVZEnvhrDV8PUJbuNANLVDHkW9LHet6Emv6lD9voLBQFTlmISVaTFqsCg..; SUBP=0033WrSXqPxfM725Ws9jqgMF55529P9D9WFGJz-6KkGP.QZIY3ZsnLh45JpX5K2hUgL.Fo24S0MXSKzESoMt; SUHB=08Jqt1FUI-smML; wb_bub_hot_1977452267=1; _s_tentry=os.51cto.com; YF-Ugrow-G0=8751d9166f7676afdce9885c6d31cd61; YF-V5-G0=731b77772529a1f49eac82a9d2c2957f; Apache=2063854313279.5806.1464923323843; ULV=1464923323886:6:2:3:2063854313279.5806.1464923323843:1464784999854; YF-Page-G0=f27a36a453e657c2f4af998bd4de9419; wb_g_minivideo_1977452267=1; UOR=,,www.izhenxin.com; TC-Ugrow-G0=370f21725a3b0b57d0baaf8dd6f16a18; wvr=6; TC-V5-G0=2bdac3b437dd23e235b79a3d6922ea06; TC-Page-G0=2b304d86df6cbca200a4b69b18c732c4"
,"Accept-Encoding: gzip, deflate, sdch"
,"Accept-Language: zh,en-US;q=0.8,en;q=0.6,zh-CN;q=0.4"
,"User-Agent: Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.7 Safari/537.36"
,"Content-Type: application/x-www-form-urlencoded"
,"Accept: */*"
,"Referer: http://www.weibo.com/u/1670458304?refer_flag=0000015010_&from=feed&loc=nickname&is_all=1"
,"X-Requested-With: XMLHttpRequest"
,"Connection: keep-alive"
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:48.0) Gecko/20100101 Firefox/48.0');

        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }

    public function headerCurl($url) {


        $headers = [
 'Host: weibo.com' ,
 'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:48.0) Gecko/20100101 Firefox/48.0' ,
 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8' ,
 'Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3' ,
 'Accept-Encoding: gzip, deflate' ,
 'Content-Type: application/x-www-form-urlencoded' ,
 'X-Requested-With: XMLHttpRequest' ,
 'Referer: http://weibo.com/u/1670458304?refer_flag=0000015010_&from=feed&loc=nickname&is_all=1' ,
 'Cookie: UOR=mozilla.com.cn,widget.weibo.com,www.izhenxin.com; SINAGLOBAL=6814434137766.561.1465068507105; ULV=1465231869225:5:5:5:1575697271665.929.1465231869192:1465148812351; SUHB=0mxFsIIj6X-B3w; un=710898853@qq.com; wb_bub_hot_1977452267=1; wb_g_minivideo_1977452267=1; TC-Ugrow-G0=e66b2e50a7e7f417f6cc12eec600f517; TC-V5-G0=28bf4f11899208be3dc10225cf7ad3c6; _s_tentry=login.sina.com.cn; Apache=1575697271665.929.1465231869192; TC-Page-G0=9183dd4bc08eff0c7e422b0d2f4eeaec; YF-V5-G0=1e772c9803ad8482528fd25e77086251; YF-Ugrow-G0=3a02f95fa8b3c9dc73c74bc9f2ca4fc6; YF-Page-G0=e44a6a701dd9c412116754ca0e3c82c3; SUB=_2A256U33-DeTxGedH7FUV9SzOzTuIHXVZKeg2rDV8PUNbuNAPLXXckW9LHetPoUuaaqDqCX1fwSNZiry8sBxwCw..; SUBP=0033WrSXqPxfM725Ws9jqgMF55529P9D9WFGJz-6KkGP.QZIY3ZsnLh45JpX5K2hUgL.Fo24S0MXSKzESoM2dJLoI7LSqPxLUPSAPXXt; wb_g_minivideo_3655689037=1; WBStore=8ca40a3ef06ad7b2|undefined; WBtopGlobal_register_version=06c53677ab86c260; SUS=SID-1977452267-1465322926-XD-0g2hq-85783df958a5395b94d12772246c8268; SUE=es%3Dbcccae4c56136d0cf0a3a84d0467b024%26ev%3Dv1%26es2%3Dff3d72a30578db923257d6ce52eb977e%26rs0%3DTdcrams1FZ3sRYaX2c7iaeQaot%252BmijTG7MiJkqsrcSvOiFl3%252FVH3hdbuH%252BrZjgm%252FntRWgi%252FecVthg5c0WbKtkK7JIR6EXT3tXYLj1RIADDPMbhA39MlBSf9Z0tSAennjq8b2u8iDoJZB0bsgPW7IixMe5TSZfKnOO1VQ0PnGbsM%253D%26rv%3D0; SUP=cv%3D1%26bt%3D1465322926%26et%3D1465409326%26d%3Dc909%26i%3D8268%26us%3D1%26vf%3D0%26vt%3D0%26ac%3D2%26st%3D0%26uid%3D1977452267%26name%3D710898853%2540qq.com%26nick%3Dyepolite%26fmp%3D%26lcp%3D2013-02-11%252023%253A47%253A18; ALF=1465927727; SSOLoginState=1465322926; wvr=6'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        // curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie); //读取cookie
        $rs = curl_exec($ch); //执行cURL抓取页面内容
        curl_close($ch);
        return $rs;
    }

}
