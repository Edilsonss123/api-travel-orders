<?php

namespace Tests\Unit\ValueObject\Travel;

use App\Exceptions\TravelException;
use App\ValueObject\Travel\TravelOrderCreateVO;
use App\ValueObject\Travel\OrderStatusVO;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class TravelOrderCreateVOTest extends TestCase
{
    /**
     * Data provider que retorna os casos de teste.
     */
    public function travelOrderDataProvider(): array
    {
        return [
            'valid_data' => [
                "John Doe",
                "Paris",
                new DateTimeImmutable("2024-12-18 21:40"),
                new DateTimeImmutable("2025-01-06 08:15"),
                OrderStatusVO::Requested,
                null
            ],
            'empty_traveler_name' => [
                "",
                "Paris",
                new DateTimeImmutable("2024-12-18 21:40"),
                new DateTimeImmutable("2025-01-06 08:15"),
                OrderStatusVO::Requested,
                'Traveler name is required.'
            ],
            'invalid_name_too_short' => [
                'Test',
                'Las Vegas',
                new DateTimeImmutable("2024-12-18 21:40"),
                new DateTimeImmutable("2025-01-06 08:15"),
                OrderStatusVO::Requested,
                'Traveler name must be at least 5 characters long.'
            ],
            'empty_destination' => [
                "John Doe",
                "",
                new DateTimeImmutable("2024-12-18 21:40"),
                new DateTimeImmutable("2025-01-06 08:15"),
                OrderStatusVO::Requested,
                'Destination is required.'
            ],
            'invalid_destination_too_short' => [
                'Test User',
                'NY',
                new DateTimeImmutable("2024-12-18 21:40"),
                new DateTimeImmutable("2025-01-06 08:15"),
                OrderStatusVO::Requested,
                'Destination must be at least 5 characters long.'
            ],
            'departure_date_in_past' => [
                "John Doe",
                "Paris",
                new DateTimeImmutable("2020-01-01 12:00"),
                new DateTimeImmutable("2025-01-06 08:15"),
                OrderStatusVO::Requested,
                'Departure date must be a future date.'
            ],
            'return_date_before_departure' => [
                "John Doe",
                "Paris",
                new DateTimeImmutable("2024-12-18 21:40"),
                new DateTimeImmutable("2024-12-17 08:15"),
                OrderStatusVO::Requested,
                'Return date must be after the departure date.'
            ],
            'invalid_status_canceled' => [
                'Test User',
                'New York',
                new DateTimeImmutable("2024-12-18 21:40"),
                new DateTimeImmutable("2025-01-06 08:15"),
                OrderStatusVO::Canceled,
                'Travel Order status cannot be canceled.'
            ],
            'invalid_name_too_long' => [
                str_repeat('Test User', 256),
                'Los Angeles',
                new DateTimeImmutable("2024-12-18 21:40"),
                new DateTimeImmutable("2025-01-06 08:15"),
                OrderStatusVO::Requested,
                'Traveler name may not be greater than 255 characters.'
            ],
            'invalid_destination_too_long' => [
                'Test User',
                str_repeat('NY - ', 256),
                new DateTimeImmutable("2024-12-18 21:40"),
                new DateTimeImmutable("2025-01-06 08:15"),
                OrderStatusVO::Requested,
                'Destination may not be greater than 255 characters.'
            ],
        ];
    }

    /**
     *
     * @dataProvider travelOrderDataProvider
     */
    public function testTravelOrderCreateVO(
        string $travelerName,
        string $destination,
        DateTimeImmutable $departureDate,
        DateTimeImmutable $returnDate,
        OrderStatusVO $status,
        ?string $expectedError
    ) {
        if ($expectedError) {
            $this->expectException(TravelException::class);
        }
        try {
            $travelOrderCreateVO = new TravelOrderCreateVO(
                $travelerName,
                $destination,
                $departureDate,
                $returnDate,
                $status
            );
        } catch (TravelException $e) {
            if ($expectedError) {
                $this->assertContains($expectedError, $e->getData());
            }

            throw $e;
        }

        if (!$expectedError) {
            $this->assertInstanceOf(TravelOrderCreateVO::class, $travelOrderCreateVO);
            $this->assertEquals($travelerName, $travelOrderCreateVO->toArray()['travelerName']);
            $this->assertEquals($destination, $travelOrderCreateVO->toArray()['destination']);
            $this->assertEquals($departureDate->format('Y-m-d H:i:s'), $travelOrderCreateVO->toArray()['departureDate']);
            $this->assertEquals($returnDate->format('Y-m-d H:i:s'), $travelOrderCreateVO->toArray()['returnDate']);
            $this->assertEquals($status->value, $travelOrderCreateVO->toArray()['status']);
        }
    }
}
