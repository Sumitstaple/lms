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
Route::group(['middleware' => 'auth:api'], function () {
	Route::get('sport', 'SportController@index');
	Route::get('sport/{id}', 'SportController@show');
	Route::post('sport', 'SportController@store');
	Route::post('teamiconsave', 'SportController@saveTeamIcon'); 
	Route::put('sport/{id}', 'SportController@update');
	Route::get('sportsforfixture', 'SportController@sportsforfixture');
	Route::get('get/sport/list', 'SportController@sportsforfixture');
 
	Route::post('round', 'RoundController@store');
	Route::get('round', 'RoundController@index');
	Route::get('sportsrounds/{id}', 'RoundController@sportsrounds');
	Route::get('round/{id}', 'RoundController@show');
	Route::put('round/{id}', 'RoundController@update');
	Route::get('fetchroundsbysport/{id}', 'RoundController@fetchroundsbysport');
	Route::get('fetchroundsbysportleague/{id}', 'RoundController@fetchroundsbysportleague');
	Route::get('roundsforfixture', 'RoundController@roundsforfixture');
	
	Route::get('team', 'TeamController@index');
	Route::get('sportsteams/{id}', 'TeamController@sportsteams');
	Route::post('team', 'TeamController@store');
	Route::get('team/{id}', 'TeamController@show');
	Route::put('team/{id}', 'TeamController@update');
	Route::get('teamsforfixture/{id}', 'TeamController@teamsforfixture');

	Route::get('fixture', 'FixtureController@index');
	Route::get('fixturebysportandround/{sid}/{rid}', 'FixtureController@fixturebysportandround');
	Route::post('fixture', 'FixtureController@store');
	Route::get('fixture/{id}', 'FixtureController@show');
	Route::put('fixture/{id}', 'FixtureController@update');
	Route::get('updateFixtureStatus/{id}', 'FixtureController@updateFixtureStatus');

	Route::get('league', 'LeagueController@index');
	Route::get('myleagues', 'LeagueController@myleagues');
	Route::post('league', 'LeagueController@store');
	Route::get('league/{id}', 'LeagueController@show');
	Route::put('league/{id}', 'LeagueController@update');
	Route::get('updateLeagueStatus/{id}', 'LeagueController@updateLeagueStatus');
	
	Route::get('manageteamlist/{id}', 'LeagueController@manageteamlist');
	Route::post('saveteamlist', 'LeagueController@saveteamlist');

	
	
	// Notification Template
	Route::get('notificationtemplates', 'NotificationTemplateController@index');

	// Notifications
	Route::get('notifications', 'NotificationController@index');
	Route::get('markasread', 'NotificationController@markasread');
	Route::get('getunread', 'NotificationController@getunread');

	// System Logs

	Route::get('systemlogs', 'SystemLogsController@index');
});