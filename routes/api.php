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


Route::get('/addkeyword', 'MainscrapingController@addkeyword_api');
Route::get('/isEnabledKeyword', 'MainscrapingController@isEnabledKeyword_api');
Route::get('/getLeads', 'MainscrapingController@getLeads_api');
Route::get('/getActive', 'MainscrapingController@getActive_api');
Route::get('/removeLead', 'MainscrapingController@removeLead_api');