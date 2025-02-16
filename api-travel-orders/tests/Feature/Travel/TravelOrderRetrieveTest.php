<?php

namespace Tests\Feature\Travel;

use App\Exceptions\TravelException;
use App\Models\Travel\OrderStatus;
use App\Models\Travel\TravelOrder;
use App\Models\User;
use App\Services\Travel\ITravelOrderService;
use App\ValueObject\Travel\OrderStatusVO;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\Feature\TestCase;

class TravelOrderRetrieveTest extends TestCase
{
    public function testStructureResponseTravelOrders()
    {
        $result = $this->withToken($this->getAuthToken())->get("/api/travel/orders");
        $result->assertStatus(200);
        $result->assertJsonStructure([
            "message", 
            "success", 
            "result" => [ 
                "orders" => [
                    "current_page",
                    "data",
                    "first_page_url",
                    "from",
                    "last_page",
                    "last_page_url",
                    "links" => [[
                        "url",
                        "label",
                        "active"
                    ]],
                    "next_page_url",
                    "path",
                    "per_page",
                    "prev_page_url",
                    "to",
                    "total"
                ]
            ]
        ]);
        $result->assertJsonIsArray("result.orders.data");
    }

    public function testGetAllTravelOrdersWithSuccess()
    {
        TravelOrder::factory()->count(5)->create();
        $result = $this->withToken($this->getAuthToken())->get("/api/travel/orders");
        $result->assertStatus(200);
        $result->assertJsonCount(5, "result.orders.data");
        $this->assertEquals(5 , $result->json("result.orders.total"));
    }

    public function testGetAllTravelOrdersFilterStatus()
    {
        $travels = TravelOrder::factory()->count(15)->create();
        $statusGroups = $travels->groupBy('status')->map(function ($group) {
            return $group->count();
        });
        
        foreach ($statusGroups as $status => $total) {
            $result = $this->withToken($this->getAuthToken())->json("GET","/api/travel/orders", [
                "status" => $status
            ]);
            $result->assertStatus(200);
            $result->assertJsonCount($total, "result.orders.data");
            $this->assertEquals($total , $result->json("result.orders.total"));
        }
    }

    /**
     * @dataProvider providerGetTravelPaginate
     */
    public function testPaginateTravelOrders($data)
    {
        TravelOrder::factory()->count(count: $data["travelOrderTotal"])->create();
        $result = $this->withToken($this->getAuthToken())->json("GET", "/api/travel/orders", $data["parametroRota"]);
        $result->assertStatus(200);
        $result->assertJsonFragment($data['result']);
    }

    public function testGetTravelOrderById() 
    {
        $travel = TravelOrder::factory()->create();
        $travel->refresh();
        
        $result = $this->withToken($this->getAuthToken())->get("/api/travel/orders/{$travel->id}");
        $result->assertStatus(200);
        $result->assertJson([
            "message" => "Sucess",
            "success" => true,
            "result" => [
              "order" => $travel->toArray()
            ]
        ]);
    }
    
    public function providerGetTravelPaginate()
    {
        return [
            "list travel orders empty" => [
                [
                    "travelOrderTotal" => 0,
                    "parametroRota" => [],
                    "result" => [
                        "current_page" => 1,
                        "last_page" => 1,
                        "per_page" => 100,
                        "to" => null,
                        "total" => 0
                    ]
                ]
            ],
            "list travel orders multiple itens" => [
                [
                    "travelOrderTotal" => 30,
                    "parametroRota" => [],
                    "result" => [
                        "current_page" => 1,
                        "last_page" => 1,
                        "per_page" => 100,
                        "to" => 30,
                        "total" => 30
                    ]
                ]
                    ],
            "list travel orders multiple itens, per_page 50 current page 2" => [
                [
                    "travelOrderTotal" => 101,
                    "parametroRota" => [
                        "page" => 2,
                        "per_page" => 50
                    ],
                    "result" => [
                        "current_page" => 2,
                        "last_page" => 3,
                        "per_page" => 50,
                        "to" => 100,
                        "total" => 101
                    ]
                ]
            ],
            "list travel orders multiple itens, per_page -7 current page -3" => [
                [
                    "travelOrderTotal" => 3,
                    "parametroRota" => [
                        "page" => -3,
                        "per_page" => -7
                    ],
                    "result" => [
                        "current_page" => 1,
                        "last_page" => 1,
                        "per_page" => 100,
                        "to" => 3,
                        "total" => 3
                    ]
                ]
            ]
        ];
    }

    /**
     * @dataProvider providerGetAllTravelOrdersWithException
     */
    public function testGetTravelOrderWithException($exception, $expectedStatus, $expectedMessage)
    {
        $mockService = Mockery::mock(ITravelOrderService::class);
        $mockService->shouldReceive('getAll')
        ->andThrow($exception);

        $this->app->instance(ITravelOrderService::class, $mockService);
        $result = $this->withToken($this->getAuthToken())->get("/api/travel/orders");
        $result->assertStatus($expectedStatus)
        ->assertJson( [
            "message" => $expectedMessage,
            "success" => false,
            "errors" => []
        ]);
    }

    public function providerGetAllTravelOrdersWithException()
    {
        return [
            "test TravelException" => [
                'exception' => new TravelException("Erro ao obter os pedidos", 500),
                'expectedStatus' => 500,
                'expectedMessage' => 'Erro ao obter os pedidos'
            ],
            "test Throwable" => [
                'exception' => new Exception("Erro desconhecido", 500),
                'expectedStatus' => 500,
                'expectedMessage' => 'Unable to process request'
            ],
        ];
    }

    public function testGetTravelOrderByIdEntityNotFound() 
    {
        $result = $this->withToken($this->getAuthToken())->get("/api/travel/orders/1");
        $result->assertStatus(404)
        ->assertJson( [
            "message" => "Entity not found",
            "success" => false,
            "errors" => []
        ]);
    }

    /**
     * @dataProvider providerGetAllTravelOrdersWithException
     */
    public function testGetTravelOrderByIdWithException($exception, $expectedStatus, $expectedMessage) 
    {
        TravelOrder::factory()->create();
        $mockService = Mockery::mock(ITravelOrderService::class);
        $mockService->shouldReceive('findById')
        ->andThrow($exception);
        $this->app->instance(ITravelOrderService::class, $mockService);

        $result = $this->withToken($this->getAuthToken())->get("/api/travel/orders/1");
        $result->assertStatus($expectedStatus)
        ->assertJson( [
            "message" => $expectedMessage,
            "success" => false,
            "errors" => []
        ]);
    }
    public function testStatusHasManyTravelOrders()
    {
        $status = OrderStatus::find(OrderStatusVO::Approved->value);
        $orders = TravelOrder::factory()->count(3)->create([
            "status" => $status->id
        ]);

        $this->assertInstanceOf(TravelOrder::class, $status->travelOrders()->first());
        $this->assertCount(3, $status->travelOrders);
    }

    public function testTravelOrderBelongsToOrderStatus()
    {
        $status = OrderStatus::find(OrderStatusVO::Requested->value);
        $order = TravelOrder::factory()->create([
            'status' => $status->id
        ]);

        $this->assertInstanceOf(OrderStatus::class, $order->travelStatus);
        $this->assertEquals($status->id, $order->travelStatus->id);
    }
    
    private function getAuthToken()
    {
        $password = uniqid("secret");
        $user = User::factory()->create([
            'password' => Hash::make($password)
        ]);
        return Auth::attempt(['email' => $user->email, 'password' => $password]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

}