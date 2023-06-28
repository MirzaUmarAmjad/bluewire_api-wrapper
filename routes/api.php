<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\zohoAPIController;


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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('getAllCompanies', 'App\Http\Controllers\zohoAPIController@getAllCompanies');
Route::get('getAllCompanyDOT', 'App\Http\Controllers\zohoAPIController@getAllCompanyDOT');
Route::get('getCompanyScore', 'App\Http\Controllers\zohoAPIController@getCompanyScore');
Route::get('getDOTScore', 'App\Http\Controllers\zohoAPIController@getDOTScore');
