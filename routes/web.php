<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
 
Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => 'web'], function () {
    Route::get(env('LARAVUE_PATH'), 'LaravueController@index')->where('any', '.*')->name('laravue');
});

Route::post('auth/{provider}', 'Auth\SocialController@SocialLogin');
Route::get('auth/{provider}/callback', 'Auth\SocialController@callback');

Route::get('callback1', 'Auth\SocialController@callback1');
Route::get('callback2', 'Auth\SocialController@callback2');

Route::get('create/test/user', 'CustomController@createUser');
Route::get('teamallocation/{team_1}/{team_2}/{team_1_fixture}/{team_2_fixture}', 'LaravueController@teamAllocation');

Route::get('teamsallocation', 'LaravueController@teamsAllocation');
Route::get('assignleaguethroughadmin/{team1}/{team2}', 'LaravueController@assignleaguethroughadmin');

Route::get('createleagues/{user_id}/{sport_id}/{round_id}/{if_forfeit}', 'LaravueController@createleague');
Route::get('joinleagues/{sport_id}', 'LaravueController@joinleagues');
Route::get('createusers', 'LaravueController@createusers');

Route::get('/cronhandler/lms_forfeit','ResultCronController@forFeitHandlerLMS');
Route::get('/cronhandler/lml_forfeit','ResultCronController@forFeitHandlerLML');
Route::get('/cronhandler/lms_result','ResultCronController@ResultLMS');
Route::get('/cronhandler/lml_result','ResultCronController@ResultLML');


Route::get('/cronhandler/undohandler','ResultCronController@undoHandler');
Route::get('calculateleaguewinner','ResultCronController@leagueWinnerHandler');



