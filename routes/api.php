<?php

use App\Http\Controllers\Api\User\AuthController;
use App\Http\Controllers\Api\User\IndexController;
use App\Http\Controllers\Api\User\MeController;
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

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth',
    'namespace' => 'User',
    'as' => 'user.'
], function ($router) {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
});

Route::group([
    'middleware' => 'api',
    'namespace' => 'User',
    'as' => 'user.'
], function ($router) {
    Route::get('/me', [MeController::class, 'me']);
});

Route::group([
    'middleware' => 'api',
    'namespace' => 'User',
    'as' => 'user.'
], function ($router) {
    Route::get('status', [IndexController::class, 'status']);
});
