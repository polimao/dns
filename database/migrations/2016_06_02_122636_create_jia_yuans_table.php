<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJiaYuansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jia_yuans', function (Blueprint $table) {

            $table->increments("id");// "": 31792439,
            $table->integer("uid");// "": 32792439,
            $table->integer("realUid");// "": 32792439,
            $table->string("nickname");// "": "俏顺馨",
            $table->string("sex");// "": "女",
            $table->string("sexValue");// "": "f",
            $table->string("randAttr");// "": "priority",
            $table->string("marriage");// "": "丧偶",
            $table->string("height");// "": "166",
            $table->string("education");// "": "本科",
            $table->string("income");// "": null,
            $table->string("work_location");// "": "大连",
            $table->string("work_sublocation");// "": "大连",
            $table->string("age");// "": 62,
            $table->string("image");// "": "http://images1.jyimg.com/w4/global/i/zchykj_f.jpg",
            $table->string("count");// "": "142",
            $table->string("online");// "": 1,
            $table->string("randTag");// "": "<span>166cm</span><span>67公斤</span>",
            $table->text("randListTag");// "": "<span>166cm</span><span>67公斤</span><span>汉族</span><span>B型血</span>",
            $table->string("userIcon");// "": "<i title=在线 class=online></i><i title=手机认证 class=tel></i>",
            $table->string("helloUrl");// "": "http://www.jiayuan.com/msg/hello.php?type=20&randomfrom=4&uhash=2740386df1d6daeaccb510dfae0b6ab5",
            $table->text("sendMsgUrl");// "": "http://www.jiayuan.com/msg/send.php?uhash=2740386df1d6daeaccb510dfae0b6ab5",
            $table->text("shortnote");// "": "    本人实际年龄是55年属羊的，因提早上学改了一岁。身份证是54年。            我出生于高干家庭，自小受到良好的教育。我是个为人和善，性格开朗，心态年       …",
            $table->text("matchCondition");// "": "59-66岁,175-188cm,本科以上,离异、丧偶"

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('jia_yuans');
    }
}
