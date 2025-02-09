<?php

use App\Http\Controllers\Api\Health\HealthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Travel\TravelOrderController;

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
Route::prefix('travel')->group(function () {
    Route::controller(HealthController::class)
        ->group(function () {
            Route::get('health', 'getStatusApi');
            Route::get('load-generator', 'loadGenerator');
        });

    Route::prefix('orders')
        ->middleware('auth:api')
        ->controller(TravelOrderController::class)
        ->group(function () {
            Route::get('', 'index');
            Route::get('status', 'showTravelStatusOrder')->name('travel.orders');
            Route::get('/{id}', 'show')->name('travel.orders');
            Route::post('', 'create');
            Route::put('/{id}/status', 'updateStatus');
        });

    Route::prefix('auth')
        ->controller(AuthController::class)
        ->group(function () {
            Route::post('login', 'login')->name('login');
            Route::post('register', 'register');
            Route::middleware('auth:api')->group(function () {
                Route::post('refresh', 'refresh');
                Route::post('logout', 'logout');
            });
        });
});
