<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'admin'], function (){
    Route::get('/current-in-cinema', 'Admin\CrawlerController@getCurrentInCinema');
    Route::get('/soon-in-cinema', 'Admin\CrawlerController@getSoonInCinema');
    Route::post('/save-projections', 'Admin\CrawlerController@postProjections');
});
