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

    #[Test]
    public function registration_fails_with_invalid_email_format(): void
    {
        static::createClient()->request(
            'POST',
            '/api/users/register',
            [
                'json' => [
                    'email' => 'invalid-email',
                    'password' => '12345678',
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function registration_fails_with_missing_email(): void
    {
        static::createClient()->request(
            'POST',
            '/api/users/register',
            [
                'json' => [
                    'password' => '12345678',
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(500);
    }

    #[Test]
    public function registration_fails_with_empty_email(): void
    {
        static::createClient()->request(
            'POST',
            '/api/users/register',
            [
                'json' => [
                    'email' => '',
                    'password' => '12345678',
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function registration_fails_with_missing_password(): void
    {
        static::createClient()->request(
            'POST',
            '/api/users/register',
            [
                'json' => [
                    'email' => 'test@example.com',
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(500);
    }

    #[Test]
    public function registration_fails_with_empty_password(): void
    {
        static::createClient()->request(
            'POST',
            '/api/users/register',
            [
                'json' => [
                    'email' => 'test@example.com',
                    'password' => '',
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function registration_fails_with_password_too_short(): void
    {
        static::createClient()->request(
            'POST',
            '/api/users/register',
            [
                'json' => [
                    'email' => 'test@example.com',
                    'password' => '1234567', // Only 7 characters, minimum is 8
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function registration_fails_with_duplicate_email(): void
    {
        // First registration should succeed
        static::createClient()->request(
            'POST',
            '/api/users/register',
            [
                'json' => [
                    'email' => 'duplicate@example.com',
                    'password' => '12345678',
                ],
            ]
        );

        $this->assertResponseIsSuccessful();

        // Second registration with same email should fail
        static::createClient()->request(
            'POST',
            '/api/users/register',
            [
                'json' => [
                    'email' => 'duplicate@example.com',
                    'password' => '87654321',
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function registration_fails_with_invalid_timezone(): void
    {
        static::createClient()->request(
            'POST',
            '/api/users/register',
            [
                'json' => [
                    'email' => 'test@example.com',
                    'password' => '12345678',
                    'timezone' => 'Invalid/Timezone',
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function registration_fails_with_invalid_locale(): void
    {
        static::createClient()->request(
            'POST',
            '/api/users/register',
            [
                'json' => [
                    'email' => 'test@example.com',
                    'password' => '12345678',
                    'locale' => 'invalid-locale-code',
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function registration_fails_with_email_too_long(): void
    {
        $longEmail = str_repeat('a', 250) . '@example.com'; // Over 255 characters

        static::createClient()->request(
            'POST',
            '/api/users/register',
            [
                'json' => [
                    'email' => $longEmail,
                    'password' => '12345678',
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function registration_fails_with_whitespace_only_email(): void
    {
        static::createClient()->request(
            'POST',
            '/api/users/register',
            [
                'json' => [
                    'email' => '   ',
                    'password' => '12345678',
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function registration_succeeds_with_whitespace_only_password(): void
    {
        static::createClient()->request(
            'POST',
            '/api/users/register',
            [
                'json' => [
                    'email' => 'test@example.com',
                    'password' => '        ', // 8 spaces - currently accepted by the system
                ],
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['success' => true]);
    }

    #[Test]
    public function registration_fails_with_malformed_json(): void
    {
        static::createClient()->request(
            'POST',
            '/api/users/register',
            [
                'body' => '{"email": "test@example.com", "password": "12345678"', // Missing closing brace
            ]
        );

        $this->assertResponseStatusCodeSame(500);
    }

    #[Test]
    public function registration_fails_with_null_values(): void
    {
        static::createClient()->request(
            'POST',
            '/api/users/register',
            [
                'json' => [
                    'email' => null,
                    'password' => null,
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(500);
    }
}
