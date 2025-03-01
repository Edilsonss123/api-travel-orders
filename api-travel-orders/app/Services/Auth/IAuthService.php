<?php

namespace App\Services\Auth;

use App\ValueObject\Auth\UserCreateVO;
use App\ValueObject\Auth\UserLoginVO;
use App\Models\User;

interface IAuthService
{
    public function register(UserCreateVO $userCreateVO): User;
    public function login(UserLoginVO $userLoginVO): array;
    public function refresh(): array;
    public function logout(): void;
}
