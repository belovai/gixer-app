<?php

namespace App\Tests\Monitor;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\UserFactory;
use PHPUnit\Framework\Attributes\Test;
use Zenstruck\Foundry\Test\Factories;

class CreateHttpMonitorTest extends ApiTestCase
{
    use Factories;

    #[Test]
    public function successfulHttpMonitorCreation(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $user = UserFactory::createOne();
        $token = $this->loginUser($client, $user->getEmail());

        $response = $client->request(
            'POST',
            $urlGenerator->generate('api_app_monitors_store'),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'name' => 'Test HTTP Monitor',
                    'description' => 'Test description',
                    'enabled' => true,
                    'interval' => 60,
                    'retryInterval' => 60,
                    'retryMax' => 2,
                    'type' => 'http',
                    'details' => [
                        'url' => 'https://example.com',
                        'httpMethod' => 'GET',
                        'expectedStatusCodes' => ['200'],
                        'timeout' => 30,
                        'maxRedirects' => 5,
                        'upsideDown' => false,
                        'ignoreSslErrors' => false,
                    ],
                ],
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['message' => 'Monitor created successfully']);
    }

    #[Test]
    public function createHttpMonitorWithInvalidUrl(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $user = UserFactory::createOne();
        $token = $this->loginUser($client, $user->getEmail());

        $response = $client->request(
            'POST',
            $urlGenerator->generate('api_app_monitors_store'),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'name' => 'Test HTTP Monitor',
                    'type' => 'http',
                    'details' => [
                        'url' => 'invalid-url',
                        'httpMethod' => 'GET',
                        'expectedStatusCodes' => ['200'],
                    ],
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function createHttpMonitorWithTooLongUrl(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $user = UserFactory::createOne();
        $token = $this->loginUser($client, $user->getEmail());

        $longUrl = 'https://'.str_repeat('a', 500).'.com';

        $client->request(
            'POST',
            $urlGenerator->generate('api_app_monitors_store'),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'name' => 'Test HTTP Monitor',
                    'type' => 'http',
                    'details' => [
                        'url' => $longUrl,
                        'httpMethod' => 'GET',
                        'expectedStatusCodes' => ['200'],
                    ],
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function createHttpMonitorWithMissingUrl(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $user = UserFactory::createOne();
        $token = $this->loginUser($client, $user->getEmail());

        $client->request(
            'POST',
            $urlGenerator->generate('api_app_monitors_store'),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'name' => 'Test HTTP Monitor',
                    'type' => 'http',
                    'details' => [
                        'httpMethod' => 'GET',
                        'expectedStatusCodes' => ['200'],
                    ],
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function createHttpMonitorWithInvalidHttpMethod(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $user = UserFactory::createOne();
        $token = $this->loginUser($client, $user->getEmail());

        $client->request(
            'POST',
            $urlGenerator->generate('api_app_monitors_store'),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'name' => 'Test HTTP Monitor',
                    'type' => 'http',
                    'details' => [
                        'url' => 'https://example.com',
                        'httpMethod' => 'INVALID_METHOD',
                        'expectedStatusCodes' => ['200'],
                    ],
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function createHttpMonitorWithMissingHttpMethod(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $user = UserFactory::createOne();
        $token = $this->loginUser($client, $user->getEmail());

        $client->request(
            'POST',
            $urlGenerator->generate('api_app_monitors_store'),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'name' => 'Test HTTP Monitor',
                    'type' => 'http',
                    'details' => [
                        'url' => 'https://example.com',
                        'expectedStatusCodes' => ['200'],
                    ],
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function createHttpMonitorWithInvalidTimeout(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $user = UserFactory::createOne();
        $token = $this->loginUser($client, $user->getEmail());

        $client->request(
            'POST',
            $urlGenerator->generate('api_app_monitors_store'),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'name' => 'Test HTTP Monitor',
                    'type' => 'http',
                    'details' => [
                        'url' => 'https://example.com',
                        'httpMethod' => 'GET',
                        'expectedStatusCodes' => ['200'],
                        'timeout' => 0, // Invalid: below minimum
                    ],
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function createHttpMonitorWithTimeoutTooHigh(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $user = UserFactory::createOne();
        $token = $this->loginUser($client, $user->getEmail());

        $client->request(
            'POST',
            $urlGenerator->generate('api_app_monitors_store'),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'name' => 'Test HTTP Monitor',
                    'type' => 'http',
                    'details' => [
                        'url' => 'https://example.com',
                        'httpMethod' => 'GET',
                        'expectedStatusCodes' => ['200'],
                        'timeout' => 3601, // Invalid: above maximum
                    ],
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function createHttpMonitorWithInvalidMaxRedirects(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $user = UserFactory::createOne();
        $token = $this->loginUser($client, $user->getEmail());

        $client->request(
            'POST',
            $urlGenerator->generate('api_app_monitors_store'),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'name' => 'Test HTTP Monitor',
                    'type' => 'http',
                    'details' => [
                        'url' => 'https://example.com',
                        'httpMethod' => 'GET',
                        'expectedStatusCodes' => ['200'],
                        'maxRedirects' => -1, // Invalid: below minimum
                    ],
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function createHttpMonitorWithMaxRedirectsTooHigh(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $user = UserFactory::createOne();
        $token = $this->loginUser($client, $user->getEmail());

        $client->request(
            'POST',
            $urlGenerator->generate('api_app_monitors_store'),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'name' => 'Test HTTP Monitor',
                    'type' => 'http',
                    'details' => [
                        'url' => 'https://example.com',
                        'httpMethod' => 'GET',
                        'expectedStatusCodes' => ['200'],
                        'maxRedirects' => 31, // Invalid: above maximum
                    ],
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function createHttpMonitorWithMissingExpectedStatusCodes(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $user = UserFactory::createOne();
        $token = $this->loginUser($client, $user->getEmail());

        $client->request(
            'POST',
            $urlGenerator->generate('api_app_monitors_store'),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'name' => 'Test HTTP Monitor',
                    'type' => 'http',
                    'details' => [
                        'url' => 'https://example.com',
                        'httpMethod' => 'GET',
                    ],
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function createHttpMonitorWithTooLongHttpBody(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $user = UserFactory::createOne();
        $token = $this->loginUser($client, $user->getEmail());

        $client->request(
            'POST',
            $urlGenerator->generate('api_app_monitors_store'),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'name' => 'Test HTTP Monitor',
                    'type' => 'http',
                    'details' => [
                        'url' => 'https://example.com',
                        'httpMethod' => 'POST',
                        'expectedStatusCodes' => ['200'],
                        'httpBody' => str_repeat('a', 5001), // Exceeds 5000 limit
                    ],
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function createHttpMonitorWithTooLongExpectedContent(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $user = UserFactory::createOne();
        $token = $this->loginUser($client, $user->getEmail());

        $client->request(
            'POST',
            $urlGenerator->generate('api_app_monitors_store'),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'name' => 'Test HTTP Monitor',
                    'type' => 'http',
                    'details' => [
                        'url' => 'https://example.com',
                        'httpMethod' => 'GET',
                        'expectedStatusCodes' => ['200'],
                        'expectedContent' => str_repeat('a', 5001), // Exceeds 5000 limit
                    ],
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function createHttpMonitorWithoutAuthentication(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $client->request(
            'POST',
            $urlGenerator->generate('api_app_monitors_store'),
            [
                'json' => [
                    'name' => 'Test HTTP Monitor',
                    'type' => 'http',
                    'details' => [
                        'url' => 'https://example.com',
                        'httpMethod' => 'GET',
                        'expectedStatusCodes' => ['200'],
                    ],
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(401);
    }

    #[Test]
    public function createHttpMonitorWithInvalidName(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $user = UserFactory::createOne();
        $token = $this->loginUser($client, $user->getEmail());

        $client->request(
            'POST',
            $urlGenerator->generate('api_app_monitors_store'),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'name' => str_repeat('a', 256), // Exceeds 255 limit
                    'type' => 'http',
                    'details' => [
                        'url' => 'https://example.com',
                        'httpMethod' => 'GET',
                        'expectedStatusCodes' => ['200'],
                    ],
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function createHttpMonitorWithInvalidInterval(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $user = UserFactory::createOne();
        $token = $this->loginUser($client, $user->getEmail());

        $client->request(
            'POST',
            $urlGenerator->generate('api_app_monitors_store'),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'name' => 'Test HTTP Monitor',
                    'interval' => 1, // Invalid: below minimum of 3
                    'type' => 'http',
                    'details' => [
                        'url' => 'https://example.com',
                        'httpMethod' => 'GET',
                        'expectedStatusCodes' => ['200'],
                    ],
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    private function loginUser($client, string $email): string
    {
        $urlGenerator = $client->getContainer()->get('router');

        $response = $client->request(
            'POST',
            $urlGenerator->generate('api_users_login'),
            [
                'json' => [
                    'email' => $email,
                    'password' => 'password',
                ],
            ]
        );

        $data = json_decode($response->getContent(), true);

        return $data['data']['token'];
    }
}
