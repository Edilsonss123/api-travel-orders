<?php

namespace Tests\Unit\Services\Auth;

use App\Models\User;
use App\Services\Auth\AuthService;
use App\Repositories\User\IUserRepository;
use App\ValueObject\Auth\UserCreateVO;
use App\ValueObject\Auth\UserLoginVO;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\TestCase;
use Mockery;
use App\Exceptions\TravelException;

class AuthServiceTest extends TestCase
{
    protected $authService;
    protected $userRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepositoryMock = Mockery::mock(IUserRepository::class);
        $this->authService = new AuthService($this->userRepositoryMock);
    }

    public function test_login_successful()
    {
        $userLoginVO = new UserLoginVO('test@example.com', 'password123');
        $hashPassword = "token_acess";

        Auth::shouldReceive('attempt')
            ->with($userLoginVO->toArray())
            ->andReturn($hashPassword);

        Auth::shouldReceive('user')
            ->andReturn((object)$userLoginVO->toArray());

        $response = $this->authService->login($userLoginVO);

        $this->assertEquals(["user", "authorization"], array_keys($response));
        $this->assertEquals($hashPassword, $response['authorization']['token']);
        $this->assertEquals('bearer', $response['authorization']['type']);
    }

    public function test_login_failed()
    {
        $userLoginVO = new UserLoginVO('test@example.com', 'wrongpassword');

        Auth::shouldReceive('attempt')
            ->with($userLoginVO->toArray())
            ->andReturn(false);

        $this->expectException(TravelException::class);
        $this->expectExceptionMessage('Unauthorized');
        $this->expectExceptionCode(401);

        $this->authService->login($userLoginVO);
    }

    public function test_register_successful()
    {
        $userCreateVO = new UserCreateVO('Test User', 'test@example.com', '123456');

        $hashPassword = strrev($userCreateVO->password);
        Hash::shouldReceive("make")->with($userCreateVO->password)
            ->andReturn($hashPassword);

        $userMocker = Mockery::mock(User::class);
        $userMocker->shouldReceive('getAttribute')
            ->with('name')
            ->andReturn($userCreateVO->name);
        $userMocker->shouldReceive('getAttribute')
            ->with('email')
            ->andReturn($userCreateVO->email);
        $userMocker->shouldReceive('getAttribute')
            ->with('password')
            ->andReturn($hashPassword);

        $this->userRepositoryMock->shouldReceive('create')
            ->with([
                'name' => $userCreateVO->name,
                'email' => $userCreateVO->email,
                'password' => $hashPassword
            ])
            ->andReturn($userMocker);


        $user = $this->authService->register($userCreateVO);
        $this->assertEquals($userMocker, $user);
    }

    public function test_refresh()
    {
        $hashPassword = "token_acess";
        Auth::shouldReceive('refresh')
            ->andReturn($hashPassword);

        Auth::shouldReceive('user')
            ->andReturn((object)['email' => 'test@example.com']);

        $response = $this->authService->refresh();

        $this->assertEquals(["user", "authorization"], array_keys($response));
        $this->assertEquals($hashPassword, $response['authorization']['token']);
        $this->assertEquals('bearer', $response['authorization']['type']);
    }

    public function test_logout()
    {
        Auth::shouldReceive('logout')
        ->once()
        ->andReturnNull();

        $this->authService->logout();

        // Auth::shouldHaveReceived('logout')->once();
        $this->assertTrue(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
