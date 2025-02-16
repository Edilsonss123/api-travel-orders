<?php
namespace App\Http\Controllers\Api\Health;

use App\Exceptions\TravelException;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Services\Health\HealthApi;
use Illuminate\Http\JsonResponse;
use Throwable;

class HealthController extends Controller
{
    private $healthApi;
    public function __construct(HealthApi $healthApi) {
        $this->healthApi = $healthApi;
    }
    public function getStatusApi(): JsonResponse
    {
        try {
            dd(database_path('database.sqlite'));
            $infoPod = $this->healthApi->getInfoPod();
            return ApiResponse::response([
                "server" => "ok",
                "status" => "healthy",
                "pod" => $infoPod["pod"],
                "ip" => $infoPod["ip"]
            ], "Healthy"); 
        } catch (Throwable $th) {
            return ApiResponse::error($th->getMessage(), []); 
        }
    }

    public function loadGenerator(): JsonResponse
    {
        try {
            // Simulando processamento pesado
            $startTime = microtime(true);
            $this->healthApi->loadGenerator();
            $endTime = microtime(true);
            $executionTime = $endTime - $startTime;
            return ApiResponse::response([
                'execution_time' => $executionTime,
                'message' => 'Load generator test completed.'
            ], "load-generator");
        } catch (Throwable $th) {
            return ApiResponse::error($th->getMessage(), []); 
        }
    }

}