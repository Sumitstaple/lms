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
	Route::get('getrounds', 'ResultController@index');
	Route::get('getfixturebyround/{id}/{s_id}', 'ResultController@getFixture');
	Route::get('setasabondon/{id}','ResultController@setAbondon');
	Route::post('saveresultstats','ResultController@saveResult');
	Route::post('undoresultround','ResultController@undoResult');
	Route::post('getleagueuser','ResultController@getLeagueUser');
	
});