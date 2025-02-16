<?php

namespace Tests\Feature\Travel;

use App\Exceptions\TravelException;
use App\Models\User;
use App\Services\Travel\IOrderStatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\Feature\TestCase;

class TravelOrderStatusTest extends TestCase
{
    use RefreshDatabase;

    public function testShowTravelStatusOrderSuccess()
    {
        $response = $this->withToken($this->getAuthToken())->get('/api/travel/orders/status');
        $response->assertStatus(200);
        $response->assertJsonStructure(["message", "success", "result" => ["orderStatus" => [["id", "status"]]]]);
    }

    public function testShowTravelStatusOrderWithTravelException()
    {
        $mockService = Mockery::mock(IOrderStatusService::class);
        $mockService->shouldReceive('getAll')->once()->andThrow(new TravelException("Erro ao buscar status", 500));
        $this->app->instance(IOrderStatusService::class, $mockService);

        $response = $this->withToken($this->getAuthToken())->get('/api/travel/orders/status');
        
        $response->assertStatus(500);
        $response->assertJson([
            "success" => false,
            "message" => "Erro ao buscar status",
            "errors" => []
        ]);
    }

    public function testShowTravelStatusOrderWithGenericException()
    {
        $mockService = Mockery::mock(IOrderStatusService::class);
        $mockService->shouldReceive('getAll')->once()->andThrow(new \Exception("Erro desconhecido", 500));
        $this->app->instance(IOrderStatusService::class, $mockService);

        $response = $this->withToken($this->getAuthToken())->get('/api/travel/orders/status');
        
        $response->assertStatus(500);
        $response->assertJson([
            "success" => false,
            "message" => "Unable to process request",
            "errors" => []
        ]);
    }
    private function getAuthToken()
    {
        $password = 'secret123';
        $user = User::factory()->create(['password' => Hash::make($password)]);
        return Auth::attempt(['email' => $user->email, 'password' => $password]);
    }
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }
}
