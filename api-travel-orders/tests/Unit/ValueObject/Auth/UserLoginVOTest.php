<?php

namespace Tests\Unit\ValueObject\Auth;

use PHPUnit\Framework\TestCase;
use App\Exceptions\TravelException;
use App\ValueObject\Auth\UserLoginVO;

class UserLoginVOTest extends TestCase
{
    /**
     * Data Provider para testar as validações de e-mail e senha.
     *
     * @return array
     */
    public function validationDataProvider()
    {
        return [
            'invalid_email_empty' => [
                '',
                'password123',
                'Email is required.'
            ],
            'invalid_email' => [
                'Test',
                'example.com',
                'Invalid email format.'
            ],
            'invalid_email_too_long' => [
                str_repeat('a', 256).'@example.com',
                'password123',
                'Email may not be greater than 255 characters.'
            ],
            'empty_password' => [
                'Test',
                '',
                'Password is required.'
            ],
            'invalid_password_too_short' => [
                'Test',
                '12',
                'Password must be at least 6 characters long.'
            ],
            'valid_data' => [
                'test@example.com',
                'password123',
                null
            ]
        ];
    }

    /**
     * Teste de criação do objeto UserLoginVO com validação de dados.
     *
     * @dataProvider validationDataProvider
     */
    public function test_create_user_validation($email, $password, $expectedErrorMessage)
    {

        if ($expectedErrorMessage) {
            $this->expectException(TravelException::class);
        }
        try {
            $userLoginVO = new UserLoginVO($email, $password);
        } catch (TravelException $e) {
            if ($expectedErrorMessage) {
                $this->assertContains($expectedErrorMessage, $e->getData());
            }
            throw $e;
        }

        if (!$expectedErrorMessage) {
            $this->assertInstanceOf(UserLoginVO::class, $userLoginVO);
            $this->assertEquals($email, $userLoginVO->email);
            $this->assertEquals($password, $userLoginVO->password);
        }
    }

    public function test_to_array_method()
    {
        $userLoginVO = new UserLoginVO('test@example.com', 'password123');

        $data = $userLoginVO->toArray();

        $this->assertIsArray($data);
        $this->assertEquals([
            'email' => 'test@example.com',
            'password' => 'password123'
        ], $data);
    }
}
