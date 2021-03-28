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



//Route::post('gett/leaguet', 'LeagueController@getLeague');
Route::get('find/league/{id}', 'LeagueController@getLeague');

Route::group(['middleware' => 'auth:api'], function () {
	// Users League


	Route::get('joinedleagues', 'UsersLeagueController@index');
	Route::get('userleaguedetail/{id}', 'UsersLeagueController@userleaguedetail');
	Route::get('currentuserleague/{userid}/{id}', 'UsersLeagueController@currentuserleague');
	Route::get('makeadmin/{leagueid}/{userid}/{status}', 'UsersLeagueController@makeadmin');
 	Route::post('sendleagueinvite', 'LeagueController@sendInvite');
 	Route::post('join/leauge/user', 'UsersLeagueController@joinUser');
  	Route::get('getleagueround/{id}','LeagueController@getRounds');
 	Route::get('getleagueroundteam/{r_id}/{s_id}/{l_id}','LeagueController@getTeams');
 	Route::get('saveteamselection/{r_id}/{l_id}/{t_id}','LeagueController@saveTeams');
    Route::get('league/join/request/accept/{id}/{uid}/{status}', 'UsersLeagueController@acceptRequest');
 	Route::post('join/leauge', 'UsersLeagueController@userJoinLeauge');
 	Route::get('leauge/find', 'LeagueController@findleaguelist');
 	Route::get('league/view/{id}', 'LeagueController@show');
 	Route::get('league/leagueinfo/{id}', 'LeagueController@leagueinfo');
 	Route::get('league/get/user/{id}', 'UsersLeagueController@getList');
 	Route::post('league/add/comment', 'LeagueCommentController@store');
 	Route::post('league/update/comment', 'LeagueCommentController@update');
 	Route::any('league/delete/comment/{id}', 'LeagueCommentController@destroy');
 	Route::get('league/get/comment/{leagueId}/{roundId}', 'LeagueCommentController@index');
 	Route::any('league/reset/comment/{id}', 'LeagueCommentController@resetCounter');

});
