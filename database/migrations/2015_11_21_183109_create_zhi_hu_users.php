<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateZhiHuUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zhi_hu_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('gender');
            $table->string('city');
            $table->string('job');
            $table->string('desc');
            $table->integer('be_favor');
            $table->integer('be_thank');
            $table->integer('asks');
            $table->integer('answers');
            $table->integer('concerned');
            $table->integer('be_concerned');
            $table->string('url');
            $table->integer('status');
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
        Schema::drop('zhi_hu_users');
    }
}
