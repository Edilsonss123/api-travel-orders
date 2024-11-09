<?php

namespace Tests\Unit\Services\Travel;

use App\Exceptions\TravelException;
use PHPUnit\Framework\TestCase;
use App\ValueObject\Auth\UserCreateVO;

class UserCreateVOTest extends TestCase
{
    /**
     * Data Provider para testar as validações de nome,e-mail e senha.
     *
     * @return array
     */
    public function validationDataProvider()
    {
        return [
            'invalid_name_empty' => [
                '',
                'test@example.com',
                'password123',
                'Traveler name is required.'
            ],
            'invalid_name_not_a_string' => [
                1,
                'test@example.com',
                'password123',
                'Traveler name must be a string.'
            ],
            'invalid_name_too_long' => [
                str_repeat('a', 256),
                'test@example.com',
                'password123',
                'Traveler name may not be greater than 255 characters.'
            ],
            'invalid_email_empty' => [
                'Test User',
                '',
                'password123',
                'Email is required.'
            ],
            'invalid_email_format' => [
                'Test User',
                'invalid-email',
                'password123',
                'Invalid email format.'
            ],
            'invalid_email_too_long' => [
                'Test User',
                str_repeat('a', 256) . '@example.com',
                'password123',
                'Email may not be greater than 255 characters.'
            ],
            'invalid_password_empty' => [
                'Test User',
                'test@example.com',
                '',
                'Password is required.'
            ],
            'invalid_password_too_short' => [
                'Test User',
                'test@example.com',
                'short',
                'Password must be at least 6 characters long.'
            ],
            'valid_data' => [
                'Test User',
                'test@example.com',
                'password123',
                null
            ]
        ];
    }

    /**
     * Teste de criação do objeto UserCreateVO com validação de dados.
     *
     * @dataProvider validationDataProvider
     */
    public function test_create_user_validation($name, $email, $password, $expectedErrorMessage)
    {
        if ($expectedErrorMessage) {
            $this->expectException(TravelException::class);
        }
        try {
            $userCreateVO = new UserCreateVO($name, $email, $password);
        } catch (Exception $e) {
            if ($expectedErrorMessage) {
                $this->assertContains($expectedErrorMessage, $e->getData());
            }
            throw $e;
        }

        if (!$expectedErrorMessage) {
            $this->assertInstanceOf(UserCreateVO::class, $userCreateVO);
            $this->assertEquals($name, $userCreateVO->name);
            $this->assertEquals($email, $userCreateVO->email);
            $this->assertEquals($password, $userCreateVO->password);
        }
    }

    public function test_to_array_method()
    {
        $userCreateVO = new UserCreateVO('Test User', 'test@example.com', 'password123');

        $data = $userCreateVO->toArray();

        $this->assertIsArray($data);
        $this->assertEquals([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123'
        ], $data);
    }
}
