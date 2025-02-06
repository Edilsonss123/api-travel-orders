<?php

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
    Route::get("health", function () {
        $podName = getHostName();
        $podIp = getHostByName(getHostName());
    
        return \App\Helpers\ApiResponse::response([
            "server" => "ok",
            "status" => "healthy",
            "pod" => $podName,
            "ip" => $podIp
        ], "Healthy"); 
    });
    
    Route::get("load-generator", function () {

        // Simulando o uso de memória
        $largeArray = [];
        for ($i = 0; $i < 1000000; $i++) {
            $largeArray[] = str_repeat("A", 1024);  // Cria strings de 1KB cada
        }
    
        // Simulando processamento pesado
        $startTime = microtime(true);
        $factorial = 1;
        for ($i = 1; $i <= 10000; $i++) {
            $factorial *= $i;  // Calcula o fatorial de 10000 (operações pesadas)
        }
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;  // Tempo de execução
    
        // Retornando a resposta com tempo de execução
        return \App\Helpers\ApiResponse::response([
            'execution_time' => $executionTime,
            'message' => 'Load generator test completed.'
        ], "load-generator");
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
