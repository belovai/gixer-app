<?php

namespace App\Tests\Auth;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use PHPUnit\Framework\Attributes\Test;

class RegisterTest extends ApiTestCase
{
    #[Test]
    public function successfulRegistration(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $client->request(
            'POST',
            $urlGenerator->generate('api_users_register'),
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
    public function successfulRegistrationWithExtraDetails(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $client->request(
            'POST',
            $urlGenerator->generate('api_users_register'),
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
    public function registrationFailsWithInvalidEmailFormat(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $client->request(
            'POST',
            $urlGenerator->generate('api_users_register'),
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
    public function registrationFailsWithMissingEmail(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $client->request(
            'POST',
            $urlGenerator->generate('api_users_register'),
            [
                'json' => [
                    'password' => '12345678',
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function registrationFailsWithEmptyEmail(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $client->request(
            'POST',
            $urlGenerator->generate('api_users_register'),
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
    public function registrationFailsWithMissingPassword(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $client->request(
            'POST',
            $urlGenerator->generate('api_users_register'),
            [
                'json' => [
                    'email' => 'test@example.com',
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function registrationFailsWithEmptyPassword(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $client->request(
            'POST',
            $urlGenerator->generate('api_users_register'),
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
    public function registrationFailsWithPasswordTooShort(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $client->request(
            'POST',
            $urlGenerator->generate('api_users_register'),
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
    public function registrationFailsWithDuplicateEmail(): void
    {
        // First registration should succeed
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $client->request(
            'POST',
            $urlGenerator->generate('api_users_register'),
            [
                'json' => [
                    'email' => 'duplicate@example.com',
                    'password' => '12345678',
                ],
            ]
        );

        $this->assertResponseIsSuccessful();

        // Second registration with same email should fail
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $client->request(
            'POST',
            $urlGenerator->generate('api_users_register'),
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
    public function registrationFailsWithInvalidTimezone(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $client->request(
            'POST',
            $urlGenerator->generate('api_users_register'),
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
    public function registrationFailsWithInvalidLocale(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $client->request(
            'POST',
            $urlGenerator->generate('api_users_register'),
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
    public function registrationFailsWithEmailTooLong(): void
    {
        $longEmail = str_repeat('a', 250).'@example.com'; // Over 255 characters

        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $client->request(
            'POST',
            $urlGenerator->generate('api_users_register'),
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
    public function registrationFailsWithWhitespaceOnlyEmail(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $client->request(
            'POST',
            $urlGenerator->generate('api_users_register'),
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
    public function registrationSucceedsWithWhitespaceOnlyPassword(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $client->request(
            'POST',
            $urlGenerator->generate('api_users_register'),
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
    public function registrationFailsWithMalformedJson(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $client->request(
            'POST',
            $urlGenerator->generate('api_users_register'),
            [
                'body' => '{"email": "test@example.com", "password": "12345678"', // Missing closing brace
            ]
        );

        $this->assertResponseStatusCodeSame(400);
    }

    #[Test]
    public function registrationFailsWithNullValues(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $client->request(
            'POST',
            $urlGenerator->generate('api_users_register'),
            [
                'json' => [
                    'email' => null,
                    'password' => null,
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }
}
