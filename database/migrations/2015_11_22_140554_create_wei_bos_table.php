<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWeiBosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wei_bos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('mid');
            $table->string('url');
            $table->string('name');
            $table->text('html');
            $table->text('content');
            $table->integer('forward_num');
            $table->integer('comment_num');
            $table->integer('like_num');
            $table->string('original_time');
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
        Schema::drop('wei_bos');
    }
}
