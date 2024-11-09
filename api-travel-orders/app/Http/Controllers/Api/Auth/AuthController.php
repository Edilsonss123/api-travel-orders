<?php

namespace App\Http\Controllers\Api\Auth;

use App\Exceptions\TravelException;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginUserRequest;
use App\Http\Requests\Auth\UserCreateRequest;
use App\Services\Auth\IAuthService;
use App\ValueObject\Auth\UserCreateVO;
use App\ValueObject\Auth\UserLoginVO;
use Throwable;

class AuthController extends Controller
{
    private IAuthService $authService;

    public function __construct(IAuthService $authService)
    {
        $this->authService = $authService;
    }
    public function login(LoginUserRequest $request)
    {
        try {
            $userLoginVO = new UserLoginVO($request->email, $request->password);
            $response = $this->authService->login($userLoginVO);
            return ApiResponse::response($response);
        } catch (TravelException $th) {
            return ApiResponse::error($th->getMessage(), [], $th->getCode());
        } catch (Throwable $th) {
            return ApiResponse::error();
        }
    }

    public function register(UserCreateRequest $request)
    {
        try {
            DB::beginTransaction();
            $userCreateVO = new UserCreateVO($request->name, $request->email, $request->password);
            $this->authService->register($userCreateVO);
            $response = $this->authService->login([
                "email" => $userCreateVO->email,
                "password" => $userCreateVO->password
            ]);
            DB::commit();
            return ApiResponse::response($response);
        } catch (TravelException $th) {
            DB::rollback();
            return ApiResponse::error($th->getMessage(), [], $th->getCode());
        } catch (Throwable $th) {
            DB::rollback();
            return ApiResponse::error();
        }
    }

    public function logout()
    {
        try {
            $this->authService->logout();
            return ApiResponse::response([], "Successfully logged out", 200);
        } catch (TravelException $th) {
            return ApiResponse::error($th->getMessage(), [], $th->getCode());
        } catch (Throwable $th) {
            return ApiResponse::error();
        }
    }

    public function refresh()
    {
        try {
            $response = $this->authService->refresh();
            return ApiResponse::response($response);
        } catch (TravelException $th) {
            return ApiResponse::error($th->getMessage(), [], $th->getCode());
        } catch (Throwable $th) {
            return ApiResponse::error();
        }
    }
}
