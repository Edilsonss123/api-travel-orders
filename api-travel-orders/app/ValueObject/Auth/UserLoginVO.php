<?php

namespace App\ValueObject\Auth;

use App\Exceptions\TravelException;

final class UserLoginVO
{
    public readonly string $email;
    public readonly string $password;

    public function __construct(
        string $email,
        string $password
    ) {
        $this->email = $email;
        $this->password = $password;
    }

    private function validate(
        string $email,
        string $password
    ): void {
        $errors = [];
        $errors[] = $this->validateEmail($email);
        $errors[] = $this->validatePassword($password);

        $errors = array_filter($errors);
        if (count($errors) > 0) {
            throw new TravelException("Invalid Data", 400, null, $errors);
        }
    }

    private function validateEmail(string $email): ?string
    {
        if (empty($email)) {
            return 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Invalid email format.';
        }
        return null;
    }

    private function validatePassword(string $password): ?string
    {
        if (empty($password)) {
            return 'Password is required.';
        }
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password
        ];
    }
}
