<?php

namespace Tests\Feature\Auth;

use App\Exceptions\TravelException;
use App\Services\Auth\IAuthService;
use Exception;
use Mockery;
use Tests\Feature\TestCase;

class UserCreateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateUserSuccess()
    {
        $response = $this->postJson("/api/travel/auth/register", [
            "name" => "teste",
            "email" => "teste@test.com",
            "password" => "teste123"
        ]);
        $response->assertStatus(200);
        $this->assertIsArray( $response->json());
        $this->assertEquals(["message", "success", "result"], array_keys($response->json()));

        $result = $response->json()["result"];
        $this->assertCount(2, $result);
        $this->assertEquals(["user", "authorization"], array_keys($result));
        $this->assertEquals(["token", "type"], array_keys($result["authorization"]));
        $this->assertEquals("bearer", $result["authorization"]["type"]);
    }

    /**
     * @dataProvider providerCreateUserFail
     */
    public function testCreateUserFail($data, $errors)
    {
        $response = $this->postJson("/api/travel/auth/register", $data);
        $response->assertStatus(400);
        $this->assertEquals([
            "message" => "Invalid Data",
            "success" => false,
            "errors" => $errors
        ], $response->json());

    }

    public function providerCreateUserFail()
    {
        return [
            "data empty" => [
                "data" => [],
                "errors" => [
                    'The name field is required.', 
                    'The email field is required.', 
                    'The password field is required.'
                ]
            ],
            "name empty" => [
                "data" => ["name" => "", "email" => "teste@gamil.com", "password" => "123456"],
                "errors" => ["The name field is required."]
            ],
            "name short" => [
                "data" => ["name" => "tes", "email" => "teste@gamil.com", "password" => "123456"],
                "errors" => ["The name must be at least 5 characters."]
            ],
            "name long" => [
                "data" => ["name" => str_repeat('a', 256), "email" => "teste@gamil.com", "password" => "123456"],
                "errors" => ["The name must not be greater than 255 characters."]
            ],
            "email empty" => [
                "data" => ["name" => "Test User", "email" => "invalid-email", "password" => "123456"],
                "errors" => ["The email must be a valid email address."]
            ],
            "email invalid" => [
                "data" => ["name" => "Test User", "email" => "", "password" => "123456"],
                "errors" => ["The email field is required."]
            ],
            "password invalid" => [
                "data" => ["name" => "Test User", "email" => "teste@gamil.com", "password" => "short"],
                "errors" => ["The password must be at least 6 characters."]
            ],
        ];
    }

    public function testCreateFailsWithTravelException()
    {
        $mockAuthService = Mockery::mock(IAuthService::class)->makePartial();
        $mockAuthService->shouldReceive('register')
        ->andThrow(new TravelException("Erro register user", 500));
    
        $this->app->instance(IAuthService::class, $mockAuthService);
        
        $response = $this->postJson("/api/travel/auth/register", [
            "name" => "teste",
            "email" => "teste@test.com",
            "password" => "teste123"
        ]);
        $response->assertStatus(500)
        ->assertJson([
            'message' => 'Erro register user'
        ]);
    }
    
    public function testCreateFailsWithThrowable()
    {
        $mockAuthService = Mockery::mock(IAuthService::class)->makePartial();
        $mockAuthService->shouldReceive('register')
        ->andThrow(new Exception(__('apiResponse.exceptionMessageError'), 500));
    
        $this->app->instance(IAuthService::class, $mockAuthService);
        
        $response = $this->postJson("/api/travel/auth/register", [
            "name" => "teste",
            "email" => "teste@test.com",
            "password" => "teste123"
        ]);
        $response->assertStatus(500)
        ->assertJson([
            'message' => __('apiResponse.exceptionMessageError')
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }
}
