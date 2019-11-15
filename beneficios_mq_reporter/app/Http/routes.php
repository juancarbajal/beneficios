<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //
});



Route::group(['prefix' => 'api/v1/','middleware' => ['api_auth']], function () {

    Route::post('reporte_crm', 'ApiController@reporte_crm');
    Route::post('reporte_crm_2', 'ApiController@reporte_crm_2');
    Route::post('guardar_crm', 'ApiController@guardar_crm');
    Route::post('reporte_descargas', 'ApiController@reporte_descargas');
});
