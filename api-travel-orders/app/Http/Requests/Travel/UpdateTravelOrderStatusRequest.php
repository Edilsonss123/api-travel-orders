<?php

namespace App\Http\Requests\Travel;

use App\Http\Requests\BaseRequest;

class UpdateTravelOrderStatusRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => 'required|exists:order_status,id'
        ];
    }
    public function attributes(): array
    {
        return __("validation.attributes.travelOrder");
    }
}
