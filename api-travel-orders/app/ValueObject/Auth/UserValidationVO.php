<?php
namespace App\ValueObject\Auth;

abstract class UserValidationVO
{
    protected function validateEmail(string $email): ?string
    {
        if (empty($email)) {
            return 'Email is required.';
        } elseif (strlen($email) > 255) {
            return 'Email may not be greater than 255 characters.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return 'Invalid email format.';
        }
        return null;
    }

    protected function validatePassword(string $password): ?string
    {
        if (empty($password)) {
            return 'Password is required.';
        }

        if (strlen($password) < 6) {
            return 'Password must be at least 6 characters long.';
        }
        return null;
    }

    
    protected function validateName(string $name): ?string
    {
        if (empty($name)) {
            return 'Traveler name is required.';
        }

        if (strlen($name) < 5) {
            return 'Traveler name must be at least 5 characters long.';
        }

        if (strlen($name) > 255) {
            return 'Traveler name may not be greater than 255 characters.';
        }

        return null;
    }
}
