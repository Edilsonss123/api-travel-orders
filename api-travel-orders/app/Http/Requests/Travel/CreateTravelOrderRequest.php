<?php

namespace App\Http\Requests\Travel;

use App\Http\Requests\BaseRequest;

class CreateTravelOrderRequest extends BaseRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'travelerName' => 'required|string|min:5|max:255',
            'destination' => 'required|string|min:5|max:255',
            'departureDate' => 'required|date|date_format:Y-m-d H:i|after:now',
            'returnDate' => 'required|date|date_format:Y-m-d H:i|after:departureDate',
            'status' => 'required|exists:order_status,id'
        ];
    }

    public function attributes(): array
    {
        return __("validation.attributes.travelOrder");
    }
}
