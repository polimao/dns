<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddConcernedToZhiHus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('zhi_hus', function (Blueprint $table) {
            $table->integer('concerned_num')->after('title');
            $table->integer('answer_num')->after('title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('zhi_hus', function (Blueprint $table) {
            $table->dropColumn('concerned_num');
            $table->dropColumn('answer_num');
        });
    }
}
