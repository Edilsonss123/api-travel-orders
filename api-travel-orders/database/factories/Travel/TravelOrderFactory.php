<?php

namespace Database\Factories\Travel;

use App\Models\Travel\TravelOrder;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\ValueObject\Travel\OrderStatusVO;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AppModelsTravelTravelOrder>
 */
class TravelOrderFactory extends Factory
{
    protected $model = TravelOrder::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'travelerName' => $this->faker->name,
            'destination' => $this->faker->city,
            'departureDate' => $this->faker->dateTime()->format('Y-m-d H:i:s'),
            'returnDate' => $this->faker->dateTimeBetween('+1 week', '+1 month')->format('Y-m-d H:i:s'),
            'status' => OrderStatusVO::from($this->faker->randomElement([1, 2, 3]))->value
        ];
    }
}
