<?php

namespace App\Tests\Auth;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use PHPUnit\Framework\Attributes\Test;

class RegisterTest extends ApiTestCase
{
    #[Test]
    public function successful_registration(): void
    {
        static::createClient()->request(
            'POST',
            '/api/users/register',
            [
                'json' => [
                    'email' => 'foo@bar.com',
                    'password' => '123456789',
                ],
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['success' => true]);
        $this->assertJsonContains([
            'data' => [
                'email' => 'foo@bar.com',
            ],
        ]);
    }

    #[Test]
    public function successful_registration_with_extra_details(): void
    {
        static::createClient()->request(
            'POST',
            '/api/users/register',
            [
                'json' => [
                    'email' => 'foo@bar.com',
                    'password' => '123456789',
                    'timezone' => 'Europe/London',
                    'locale' => 'hu',
                ],
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['success' => true]);
        $this->assertJsonContains([
            'data' => [
                'email' => 'foo@bar.com',
                'timezone' => 'Europe/London',
                'locale' => 'hu',
            ],
        ]);
    }
}
