<?php

namespace Tests\Feature\Travel;

use App\Exceptions\TravelException;
use App\Models\Travel\OrderStatus;
use App\Models\Travel\TravelOrder;
use App\Models\User;
use App\Services\Travel\ITravelOrderService;
use App\ValueObject\Travel\OrderStatusVO;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\Feature\TestCase;

class TravelOrderCreateTest extends TestCase
{
    public function testCreateTravelOrderSuccess()
    {
        $token = $this->getAuthToken();
        
        $requestData = [
            'travelerName' => 'John Doe',
            'destination' => 'New York',
            'departureDate' => now()->addHour(3)->format('Y-m-d H:i'),
            'returnDate' => now()->addDays(7)->format('Y-m-d H:i'),
            'status' => OrderStatusVO::Requested->value
        ];

        $response = $this->withToken($token)->post('/api/travel/orders', $requestData);
        $response->assertStatus(201);
        $response->assertHeader('Location');
        $this->assertDatabaseHas('travel_orders', [
            'travelerName' => 'John Doe',
            'destination' => 'New York'
        ]);
    }

    public function testCreateTravelOrderValidationError()
    {
        $token = $this->getAuthToken();

        $requestData = [
            'travelerName' => '',
            'destination' => '',
            'departureDate' => 'invalid-date',
            'returnDate' => 'invalid-date',
            'status' => 'invalid-status'
        ];

        $response = $this->withToken($token)->post('/api/travel/orders', $requestData);
        
        $response->assertStatus(400);
        $this->assertEquals( [
            "The traveler name field is required.",
            "The destination field is required.",
            "The departure date is not a valid date.",
            "The departure date does not match the format Y-m-d H:i.",
            "The departure date must be a date after now.",
            "The return date is not a valid date.",
            "The return date does not match the format Y-m-d H:i.",
            "The return date must be a date after departure date.",
            "The selected order status is invalid."
        ], $response->json("errors"));
    }

    /**
     * @dataProvider providerCreateTravelOrderWithException
     */
    public function testCreateTravelOrderWithException($exception, $expectedStatus, $expectedMessage)
    {
        $token = $this->getAuthToken();
        
        $mockService = Mockery::mock(ITravelOrderService::class);
        $mockService->shouldReceive('create')
        ->andThrow($exception);
        $this->app->instance(ITravelOrderService::class, $mockService);

        $requestData = [
            'travelerName' => 'John Doe',
            'destination' => 'New York',
            'departureDate' => now()->addHour(2)->format('Y-m-d H:i'),
            'returnDate' => now()->addDays(7)->format('Y-m-d H:i'),
            'status' => OrderStatusVO::Requested->value
        ];

        $response = $this->withToken($token)->post('/api/travel/orders', $requestData);
        
        $response->assertStatus($expectedStatus);
        $response->assertJson(['success' => false, 'message' => $expectedMessage]);
    }
    public function providerCreateTravelOrderWithException()
    {
        return [
            "test TravelException" => [
                new TravelException("Erro ao criar pedido", 500),
                500,
                "Erro ao criar pedido"
            ],
            "test Generic Exception" => [
                new \Exception("Erro inesperado", 500),
                500,
                "Unable to process request"
            ],
        ];
    }
    
    public function testCreateTravelOrderWithPastReturnDate()
    {
        $token = $this->getAuthToken();

        $requestData = [
            'travelerName' => 'Alice Doe',
            'destination' => 'Los Angeles',
            'departureDate' => now()->addHour(1)->format('Y-m-d H:i'),
            'returnDate' => now()->subDay()->format('Y-m-d H:i'),
            'status' => OrderStatusVO::Requested->value
        ];

        $response = $this->withToken($token)->post('/api/travel/orders', $requestData);
        
        $response->assertStatus(400);
        $this->assertEquals(  [
            "The return date must be a date after departure date."
        ], $response->json("errors"));
    }

    public function testCreateTravelOrderWithDifferentValidStatus()
    {
        $token = $this->getAuthToken();
        
        $requestData = [
            'travelerName' => 'Alice Doe',
            'destination' => 'Los Angeles',
            'departureDate' => now()->addMinute(5)->format('Y-m-d H:i'),
            'returnDate' => now()->addDays(5)->format('Y-m-d H:i'),
            'status' => OrderStatusVO::Approved->value
        ];

        $response = $this->withToken($token)->post('/api/travel/orders', $requestData);
        
        $response->assertStatus(201);
        $this->assertDatabaseHas('travel_orders', [
            'travelerName' => 'Alice Doe',
            'status' => OrderStatusVO::Approved->value
        ]);
    }

    public function testCreateTravelOrderWithoutAuthentication()
    {
        $requestData = [
            'travelerName' => 'Bob Doe',
            'destination' => 'Miami',
            'departureDate' => now()->addHour(1)->format('Y-m-d H:i'),
            'returnDate' => now()->addDays(3)->format('Y-m-d H:i'),
            'status' => OrderStatusVO::Requested->value
        ];

        $response = $this->post('/api/travel/orders', $requestData);
        
        $response->assertStatus(401);
    }

    private function getAuthToken()
    {
        $password = 'secret123';
        $user = User::factory()->create([ 'password' => Hash::make($password) ]);
        return Auth::attempt(['email' => $user->email, 'password' => $password]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

}