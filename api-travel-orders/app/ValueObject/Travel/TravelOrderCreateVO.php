<?php

namespace App\ValueObject\Travel;

use App\Exceptions\TravelException;
use DateTimeImmutable;

final class TravelOrderCreateVO
{
    public readonly string $travelerName;
    public readonly string $destination;
    public readonly DateTimeImmutable $departureDate;
    public readonly DateTimeImmutable $returnDate;
    public readonly OrderStatusVO $status;
    public function __construct(
        string $travelerName,
        string $destination,
        DateTimeImmutable $departureDate,
        DateTimeImmutable $returnDate,
        OrderStatusVO $status
    ) {
        $this->validate($travelerName, $destination, $departureDate, $returnDate, $status);

        $this->travelerName = $travelerName;
        $this->destination = $destination;
        $this->departureDate = $departureDate;
        $this->returnDate = $returnDate;
        $this->status = $status;
    }

    private function validate(
        string $travelerName,
        string $destination,
        DateTimeImmutable $departureDate,
        DateTimeImmutable $returnDate,
        OrderStatusVO $status
    ): void {
        $errors = [];
        $errors[] = $this->validateTravelerName($travelerName);
        $errors[] = $this->validateDestination($destination);
        $errors[] = $this->validateDepartureDate($departureDate);
        $errors[] = $this->validateReturnDate($returnDate, $departureDate);
        $errors[] = $this->validateStatus($status);
        $errors = array_filter($errors);
        if (count($errors) > 0) {
            throw new TravelException("Invalid Data", 400, null, $errors);
        }
    }

    private function validateTravelerName(string $travelerName): string|null
    {
        if (empty($travelerName)) {
            return 'Traveler name is required.';
        } elseif (strlen($travelerName) < 5) {
            return 'Traveler name must be at least 5 characters long.';
        } elseif (strlen($travelerName) > 255) {
            return 'Traveler name may not be greater than 255 characters.';
        }
        return null;
    }

    private function validateDestination(string $destination): null|string
    {
        if (empty($destination)) {
            return 'Destination is required.';
        } elseif (strlen($destination) < 5) {
            return 'Destination must be at least 5 characters long.';
        } elseif (strlen($destination) > 255) {
            return 'Destination may not be greater than 255 characters.';
        }
        return null;
    }

    private function validateDepartureDate(DateTimeImmutable $departureDate): null|string
    {
        if ($departureDate <= new DateTimeImmutable()) {
            return 'Departure date must be a future date.';
        }
        return null;
    }

    private function validateReturnDate(DateTimeImmutable $returnDate, DateTimeImmutable $departureDate): null|string
    {
        if ($returnDate <= $departureDate) {
            return 'Return date must be after the departure date.';
        }
        return null;
    }

    private function validateStatus(OrderStatusVO $status): ?string
    {
        if ($status->value === OrderStatusVO::Canceled->value) {
            return 'Travel Order status cannot be canceled.';
        }
        return null;
    }

    public function toArray(): array
    {
        return [
            'travelerName' => $this->travelerName,
            'destination' => $this->destination,
            'departureDate' => $this->departureDate->format('Y-m-d H:i:s'),
            'returnDate' => $this->returnDate->format('Y-m-d H:i:s'),
            'status' => $this->status->value,
        ];
    }
}
