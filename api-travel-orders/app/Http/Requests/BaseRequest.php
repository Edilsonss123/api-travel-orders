<?php

namespace App\Http\Requests;

use App\Helpers\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;

class BaseRequest extends FormRequest
{
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException(
            $validator,
            ApiResponse::error(__('apiResponse.exceptionMessageInvalidValidation'), $validator->errors()->all(), 400)
        );
    }
}
