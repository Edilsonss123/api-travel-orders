<?php

namespace Tests\Feature\Auth;

use App\Exceptions\TravelException;
use App\Models\User;
use App\Services\Auth\IAuthService;
use Exception;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\Feature\TestCase;

class UserAuthenticateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }
    
    public function testUserCanAuthenticateWithValidCredentials()
    {
        $user = User::factory()->create([
            'password' => Hash::make($password = 'secret123')
        ]);

        $response = $this->postJson('/api/travel/auth/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                "result" => ["authorization" => ['token']]
            ]);
    }

    public function testUserCannotAuthenticateWithInvalidCredentials()
    {
        $user = User::factory()->create([
            'password' => Hash::make($password = 'secret123')
        ]);
        $response = $this->postJson('/api/travel/auth/login', [
            'email' => $user->email,
            'password' => "sdfsdfsd",
        ]);

        $response->assertStatus(401)
        ->assertJson(['message' => 'Unauthorized']);
    }
    public function testUserCanAuthenticateWithTravelException()
    {
        $mockAuthService = Mockery::mock(IAuthService::class)->makePartial();
        $mockAuthService->shouldReceive('login')
        ->andThrow(new TravelException("Failed Login", 500));
    
        $this->app->instance(IAuthService::class, $mockAuthService);

        $user = User::factory()->create([
            'password' => Hash::make($password = 'secret123')
        ]);

        $response = $this->postJson('/api/travel/auth/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertStatus(500)
        ->assertJson([
            'message' => "Failed Login"
        ]);
    }

    public function testUserCanAuthenticateWithThrowable()
    {
        $mockAuthService = Mockery::mock(IAuthService::class)->makePartial();
        $mockAuthService->shouldReceive('login')
        ->andThrow(new Exception(__('apiResponse.exceptionMessageError'), 500));
    
        $this->app->instance(IAuthService::class, $mockAuthService);

        $user = User::factory()->create([
            'password' => Hash::make($password = 'secret123')
        ]);

        $response = $this->postJson('/api/travel/auth/login', [
            'email' => $user->email,
            'password' => $password,
        ]);


        $response->assertStatus(500)
        ->assertJson([
            'message' => __('apiResponse.exceptionMessageError')
        ]);
    }

    public function testUserCannotAuthenticateWithoutCredentials()
    {
        $response = $this->postJson('/api/travel/auth/login', []);
        $response->assertStatus(400);

        $this->assertEquals([
            "message" => "Invalid Data",
            "success" => false,
            "errors" => [
                "The email field is required.",
                "The password field is required."
            ]
        ], $response->json());
    }

    public function testLogoutSuccess()
    {
        $user = User::factory()->create([
            'password' => Hash::make('secret123')
        ]);

        $response = $this->postJson('/api/travel/auth/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $token = $response->json('result.authorization.token');
        $response = $this->withToken($token)
        ->postJson('/api/travel/auth/logout');

        $response->assertStatus(200)
        ->assertJson([
            'message' => 'Successfully logged out'
        ]);
    }

    public function testLogoutFailsWhenNotAuthenticated()
    {
        $response = $this->postJson('/api/travel/auth/logout');
        $response->assertStatus(401)
        ->assertJson([
            'message' => 'Unauthenticated.'
        ]);
    }

    public function testLogoutFailsWithTravelException()
    {
        $user = User::factory()->create([
            'password' => Hash::make('secret123')
        ]);

        $response = $this->postJson('/api/travel/auth/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $token = $response->json('result.authorization.token');

        $mockAuthService = Mockery::mock(IAuthService::class)->makePartial();
        $mockAuthService->shouldReceive('logout')
        ->andThrow(new TravelException("Logout failed", 500));
    
        $this->app->instance(IAuthService::class, $mockAuthService);

        $response = $this->withToken($token)
        ->postJson('/api/travel/auth/logout');

        $response->assertStatus(500)
        ->assertJson([
            'message' => 'Logout failed'
        ]);
    }

    public function testLogoutFailsWithThrowable()
    {
        $user = User::factory()->create([
            'password' => Hash::make('secret123')
        ]);

        $response = $this->postJson('/api/travel/auth/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $token = $response->json('result.authorization.token');

        $mockAuthService = Mockery::mock(IAuthService::class)->makePartial();
        $mockAuthService->shouldReceive('logout')
        ->andThrow(new Exception(__('apiResponse.exceptionMessageError'), 500));
    
        $this->app->instance(IAuthService::class, $mockAuthService);

        $response = $this->withToken($token)
        ->postJson('/api/travel/auth/logout');

        $response->assertStatus(500)
        ->assertJson([
            'message' => __('apiResponse.exceptionMessageError')
        ]);
    }

    public function testRefreshSuccess()
    {
        $user = User::factory()->create([
            'password' => Hash::make('secret123')
        ]);

        $response = $this->postJson('/api/travel/auth/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $token = $response->json('result.authorization.token');
        $response = $this->withToken($token)->postJson('/api/travel/auth/refresh');
        
        $response->assertStatus(status: 200)
        ->assertJsonStructure([
            "result" => ["user", "authorization" => ['token', 'type']]
        ]);
    }

    public function testRefreshFailureWithUnauthenticated()
    {
        $response = $this->postJson('/api/travel/auth/refresh');

        $response->assertStatus(401)
        ->assertJson([
            'message' => 'Unauthenticated.'
        ]);
    }

    public function testRefreshFailureWithTravelException()
    {
        $user = User::factory()->create([
            'password' => Hash::make('secret123')
        ]);

        $response = $this->postJson('/api/travel/auth/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $token = $response->json('result.authorization.token');

        $mockAuthService = Mockery::mock(IAuthService::class)->makePartial();
        $mockAuthService->shouldReceive('refresh')
        ->andThrow(new TravelException("Erro ao tentar atualizar o token", 500));

        $this->app->instance(IAuthService::class, $mockAuthService);

        $response = $this->withToken($token)->postJson('/api/travel/auth/refresh');

        $response->assertStatus(500)
        ->assertJson([
            'message' => 'Erro ao tentar atualizar o token'
        ]);
    }

    public function testRefreshFailureWithThrowable()
    {
        $user = User::factory()->create([
            'password' => Hash::make('secret123')
        ]);

        $response = $this->postJson('/api/travel/auth/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ]);

        $token = $response->json('result.authorization.token');

        $mockAuthService = Mockery::mock(IAuthService::class)->makePartial();
        $mockAuthService->shouldReceive('refresh')
        ->andThrow(new Exception(__('apiResponse.exceptionMessageError'), 500));


        $this->app->instance(IAuthService::class, $mockAuthService);

        $response = $this->withToken($token)->postJson('/api/travel/auth/refresh');

        $response->assertStatus(500)
        ->assertJson([
            'message' =>  __('apiResponse.exceptionMessageError')
        ]);
    }

}