<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    $zhi_hus = App\Http\ZhiHu::orderBy('concerned_num','desc')->limit(50)->get();

    return $zhi_hus;
});
