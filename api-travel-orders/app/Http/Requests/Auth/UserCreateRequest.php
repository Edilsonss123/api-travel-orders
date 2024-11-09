<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\BaseRequest;

class UserCreateRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:5|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6'
        ];
    }
}
