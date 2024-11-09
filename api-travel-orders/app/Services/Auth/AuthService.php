<?php

namespace App\Services\Auth;

use App\Exceptions\TravelException;
use App\Models\User;
use App\Repositories\User\IUserRepository;
use App\Services\Auth\IAuthService;
use App\ValueObject\Auth\UserCreateVO;
use App\ValueObject\Auth\UserLoginVO;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthService implements IAuthService
{
    private IUserRepository $userRepository;
    public function __construct(IUserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    public function login(UserLoginVO $userLoginVO): array
    {
        $token = Auth::attempt($userLoginVO->toArray());
        if (!$token) {
            throw new TravelException('Unauthorized', 401);
        }
        return [
            'user' => Auth::user(),
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ];
    }

    public function register(UserCreateVO $userCreateVO): User
    {
        $user = $this->userRepository->create([
            'name' => $userCreateVO->name,
            'email' => $userCreateVO->email,
            'password' => Hash::make($userCreateVO->password),
        ]);

        return $user;
    }

    public function refresh(): array
    {
        return [
            'user' => Auth::user(),
            'authorization' => [
                'token' => Auth::refresh(),
                'type' => 'bearer',
            ]
        ];
    }

    public function logout(): void
    {
        Auth::logout();
    }
}
