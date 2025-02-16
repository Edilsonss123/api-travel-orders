<?php

namespace Tests\Feature\Travel;

use App\Exceptions\TravelException;
use App\Models\Travel\TravelOrder;
use App\Models\User;
use App\Services\Travel\ITravelOrderService;
use App\ValueObject\Travel\OrderStatusVO;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\Feature\TestCase;

class TravelOrderUpdateStatusTest extends TestCase
{
    public function testUpdateTravelOrderStatusSuccess()
    {
        $token = $this->getAuthToken();
        
        $orderTravel = TravelOrder::factory()->create([
            "status" => OrderStatusVO::Requested->value
        ]);

        $response = $this->withToken($token)->put("/api/travel/orders/{$orderTravel->id}/status", ['status' => OrderStatusVO::Approved->value]);
        $orderTravel->status =  OrderStatusVO::Approved->value;
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $this->assertEquals($response->json("result.order"), $orderTravel->toArray());
    }

    public function testUpdateTravelOrderStatusValidationError()
    {
        $token = $this->getAuthToken();

        $response = $this->withToken($token)->put('/api/travel/orders/1/status', ['status' => 'invalid-status']);
        
        $response->assertStatus(400);
        $this->assertEquals([
            "The selected order status is invalid."
        ], $response->json("errors"));
    }

    /**
     * @dataProvider providerUpdateTravelOrderStatusWithException
     */
    public function testUpdateTravelOrderStatusWithException($exception, $expectedStatus, $expectedMessage)
    {
        $token = $this->getAuthToken();
        
        $mockService = Mockery::mock(ITravelOrderService::class);
        $mockService->shouldReceive('updateStatus')
            ->andThrow($exception);
        $this->app->instance(ITravelOrderService::class, $mockService);

        $response = $this->withToken($token)->put('/api/travel/orders/1/status', ['status' => OrderStatusVO::Approved->value]);
        
        $response->assertStatus($expectedStatus);
        $response->assertJson(['success' => false, 'message' => $expectedMessage]);
    }

    public function providerUpdateTravelOrderStatusWithException()
    {
        return [
            "test TravelException" => [
                new TravelException("Erro ao atualizar status", 500),
                500,
                "Erro ao atualizar status"
            ],
            "test Generic Exception" => [
                new \Exception("Erro inesperado", 500),
                500,
                "Unable to process request"
            ],
        ];
    }

    public function testUpdateTravelOrderStatusWithoutAuthentication()
    {
        $response = $this->put('/api/travel/orders/1/status', ['status' => OrderStatusVO::Approved->value]);
        
        $response->assertStatus(401);
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
