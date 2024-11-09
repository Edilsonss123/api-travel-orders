<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;

class LoginUserRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|string|email',
            'password' => 'required|string'
        ];
    }
}
