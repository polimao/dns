<?php

namespace App\Console\Commands;

use App\Console\Boot;
use App\Console\Commands\Crawler;
use App\Http\WeiBo;

class CrawlerWeiBo extends Boot
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
        
        $this->grab();

        $this->end();
    }

    public function grab()
    {
        $url = 'http://weibo.com/p/aj/v6/mblog/mbloglist?ajwvr=6&domain=100505&refer_flag=0000015010_&from=feed&loc=nickname&is_all=1&pagebar=0&pl_name=Pl_Official_MyProfileFeed__24&id=1005051670458304&script_uri=/u/1670458304&feed_type=0&page=1&pre_page=1&domain_op=100505&__rnd=1465322949551';
        $result = $this->cacertCurl($url);

        $result = iconv('gbk','utf-8//IGNORE', $result);

        file_put_contents('./tttt.html', $result);
        dd($result);
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

        $data = curl_exec($ch);



     //        $headers = [
     // 'Host: weibo.com' ,
     // 'User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:48.0) Gecko/20100101 Firefox/48.0' ,
     // 'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8' ,
     // 'Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3' ,
     // 'Accept-Encoding: gzip, deflate' ,
     // 'Content-Type: application/x-www-form-urlencoded' ,
     // 'X-Requested-With: XMLHttpRequest' ,
     // 'Referer: http://weibo.com/u/1670458304?refer_flag=0000015010_&from=feed&loc=nickname&is_all=1' ,
     // 'Cookie: UOR=mozilla.com.cn,widget.weibo.com,www.izhenxin.com; SINAGLOBAL=6814434137766.561.1465068507105; ULV=1465231869225:5:5:5:1575697271665.929.1465231869192:1465148812351; SUHB=0mxFsIIj6X-B3w; un=710898853@qq.com; wb_bub_hot_1977452267=1; wb_g_minivideo_1977452267=1; TC-Ugrow-G0=e66b2e50a7e7f417f6cc12eec600f517; TC-V5-G0=28bf4f11899208be3dc10225cf7ad3c6; _s_tentry=login.sina.com.cn; Apache=1575697271665.929.1465231869192; TC-Page-G0=9183dd4bc08eff0c7e422b0d2f4eeaec; YF-V5-G0=1e772c9803ad8482528fd25e77086251; YF-Ugrow-G0=3a02f95fa8b3c9dc73c74bc9f2ca4fc6; YF-Page-G0=e44a6a701dd9c412116754ca0e3c82c3; SUB=_2A256U33-DeTxGedH7FUV9SzOzTuIHXVZKeg2rDV8PUNbuNAPLXXckW9LHetPoUuaaqDqCX1fwSNZiry8sBxwCw..; SUBP=0033WrSXqPxfM725Ws9jqgMF55529P9D9WFGJz-6KkGP.QZIY3ZsnLh45JpX5K2hUgL.Fo24S0MXSKzESoM2dJLoI7LSqPxLUPSAPXXt; wb_g_minivideo_3655689037=1; WBStore=8ca40a3ef06ad7b2|undefined; WBtopGlobal_register_version=06c53677ab86c260; SUS=SID-1977452267-1465322926-XD-0g2hq-85783df958a5395b94d12772246c8268; SUE=es%3Dbcccae4c56136d0cf0a3a84d0467b024%26ev%3Dv1%26es2%3Dff3d72a30578db923257d6ce52eb977e%26rs0%3DTdcrams1FZ3sRYaX2c7iaeQaot%252BmijTG7MiJkqsrcSvOiFl3%252FVH3hdbuH%252BrZjgm%252FntRWgi%252FecVthg5c0WbKtkK7JIR6EXT3tXYLj1RIADDPMbhA39MlBSf9Z0tSAennjq8b2u8iDoJZB0bsgPW7IixMe5TSZfKnOO1VQ0PnGbsM%253D%26rv%3D0; SUP=cv%3D1%26bt%3D1465322926%26et%3D1465409326%26d%3Dc909%26i%3D8268%26us%3D1%26vf%3D0%26vt%3D0%26ac%3D2%26st%3D0%26uid%3D1977452267%26name%3D710898853%2540qq.com%26nick%3Dyepolite%26fmp%3D%26lcp%3D2013-02-11%252023%253A47%253A18; ALF=1465927727; SSOLoginState=1465322926; wvr=6'
     //        ];
     //        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
     //        curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
     //        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:48.0) Gecko/20100101 Firefox/48.0');
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
