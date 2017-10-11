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
    Route::group(['prefix' => 'crawler'], function () {
        Route::get('/current-in-cinema', 'Admin\CrawlerController@getCurrentInCinema');
        Route::post('/current-in-cinema', 'Admin\MovieCrawlerController@postCurrentInCinema');
        Route::get('/current-in-cinemas', 'Admin\CrawlerController@getAllCurrentInCinemas');
        Route::get('/soon-in-cinema', 'Admin\CrawlerController@getSoonInCinema');
    });
    Route::post('/add-one-movie', 'Admin\MovieController@postMovieFromTmdb');
    Route::post('/save-projections', 'Admin\CrawlerController@postProjections');
    Route::post('/save-movie-tmdb', 'Admin\MovieController@postMovieFromTmdb');
});
