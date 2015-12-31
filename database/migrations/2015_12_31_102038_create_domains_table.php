<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domains', function (Blueprint $table) {
            $table->increments('id');
            $table->string('word')->index();
            $table->string('word_len');
            $table->string('pinyin')->index();
            $table->string('pinyin_len');
            $table->integer('available');
            $table->integer('status');
            $table->integer('entry_cnt');
            $table->integer('quyer_cnt');
            $table->integer('quyer_status');
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
        Schema::drop('domains');
    }
}
