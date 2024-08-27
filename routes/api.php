<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get('/',  [ AuthController::class , 'me'])->middleware('JwtToken');
Route::post('/generate_token',  [ AuthController::class , 'getToken']);

Route::group(['prefix' => 'users',  'middleware' => 'JwtToken'], function(){
    Route::get('/',  [ UserController::class , 'getUsers']);
    Route::post('/',  [ UserController::class , 'userStore']);
});