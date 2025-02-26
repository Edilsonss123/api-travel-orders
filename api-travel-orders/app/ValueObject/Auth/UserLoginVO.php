<?php

namespace App\ValueObject\Auth;

use App\Exceptions\TravelException;

final class UserLoginVO extends UserValidationVO
{
    public readonly string $email;
    public readonly string $password;

    public function __construct(
        string $email,
        string $password
    ) {
        $this->validate($email, $password);

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

        $errors = array_values(array_filter($errors));
        if (!empty($errors)) {
            throw new TravelException("Invalid Data", 400, null, $errors);
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
