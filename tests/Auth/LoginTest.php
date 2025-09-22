<?php

namespace App\Tests\Auth;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\UserFactory;
use PHPUnit\Framework\Attributes\Test;
use Zenstruck\Foundry\Test\Factories;

class LoginTest extends ApiTestCase
{
    use Factories;

    #[Test]
    public function successfulLogin(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $user = UserFactory::createOne();
        $response = $client->request(
            'POST',
            $urlGenerator->generate('api_users_login'),
            [
                'json' => [
                    'email' => $user->getEmail(),
                    'password' => 'password',
                ],
            ]
        );

        $data = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['success' => true]);
        $this->assertNotEmpty($data['data']['token']);
    }

    #[Test]
    public function loginWithInvalidPassword(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $user = UserFactory::createOne();
        $client->request(
            'POST',
            $urlGenerator->generate('api_users_login'),
            [
                'json' => [
                    'email' => $user->getEmail(),
                    'password' => 'wrong_password',
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains(['message' => 'Invalid credentials.']);
    }

    #[Test]
    public function loginWithNonExistentUser(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $client->request(
            'POST',
            $urlGenerator->generate('api_users_login'),
            [
                'json' => [
                    'email' => 'nonexistent@example.com',
                    'password' => 'password',
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains(['message' => 'Invalid credentials.']);
    }

    #[Test]
    public function loginWithEmptyEmail(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $client->request(
            'POST',
            $urlGenerator->generate('api_users_login'),
            [
                'json' => [
                    'email' => '',
                    'password' => 'password',
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function loginWithEmptyPassword(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $user = UserFactory::createOne();
        $client->request(
            'POST',
            $urlGenerator->generate('api_users_login'),
            [
                'json' => [
                    'email' => $user->getEmail(),
                    'password' => '',
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function loginWithInvalidEmailFormat(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $client->request(
            'POST',
            $urlGenerator->generate('api_users_login'),
            [
                'json' => [
                    'email' => 'invalid-email',
                    'password' => 'password',
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function loginWithMissingCredentials(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $client->request(
            'POST',
            $urlGenerator->generate('api_users_login'),
            [
                'json' => [
                    'email' => null,
                    'password' => null,
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function loginWithDisabledUser(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $user = UserFactory::createOne(['enabled' => false]);
        $client->request(
            'POST',
            $urlGenerator->generate('api_users_login'),
            [
                'json' => [
                    'email' => $user->getEmail(),
                    'password' => 'password',
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains(['message' => 'User account is disabled.']);
    }
}
