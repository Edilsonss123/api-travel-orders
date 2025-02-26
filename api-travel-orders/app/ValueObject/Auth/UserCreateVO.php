<?php

namespace App\ValueObject\Auth;

use App\Exceptions\TravelException;

final class UserCreateVO extends UserValidationVO
{
    public readonly string $name;
    public readonly string $email;
    public readonly string $password;

    public function __construct(
        string $name,
        string $email,
        string $password
    ) {
        $this->validate($name, $email, $password);

        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }

    private function validate(
        string $name,
        string $email,
        string $password
    ): void {
        $errors = [];
        $errors[] = $this->validateName($name);
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
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password
        ];
    }
}
