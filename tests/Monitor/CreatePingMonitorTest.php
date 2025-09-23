<?php

namespace App\Tests\Monitor;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\UserFactory;
use PHPUnit\Framework\Attributes\Test;
use Zenstruck\Foundry\Test\Factories;

class CreatePingMonitorTest extends ApiTestCase
{
    use Factories;

    #[Test]
    public function successfulPingMonitorCreation(): void
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
                    'name' => 'Test Ping Monitor',
                    'description' => 'Test description',
                    'enabled' => true,
                    'interval' => 60,
                    'retryInterval' => 60,
                    'retryMax' => 2,
                    'type' => 'ping',
                    'details' => [
                        'hostname' => 'google.com',
                        'packetSize' => 64,
                    ],
                ],
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['message' => 'Monitor created successfully']);
    }

    #[Test]
    public function createPingMonitorWithValidIpAddress(): void
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
                    'name' => 'Test Ping Monitor IP',
                    'type' => 'ping',
                    'details' => [
                        'hostname' => '8.8.8.8',
                        'packetSize' => 56,
                    ],
                ],
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['message' => 'Monitor created successfully']);
    }

    #[Test]
    public function createPingMonitorWithMissingHostname(): void
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
                    'name' => 'Test Ping Monitor',
                    'type' => 'ping',
                    'details' => [
                        'packetSize' => 64,
                    ],
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function createPingMonitorWithEmptyHostname(): void
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
                    'name' => 'Test Ping Monitor',
                    'type' => 'ping',
                    'details' => [
                        'hostname' => '',
                        'packetSize' => 64,
                    ],
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function createPingMonitorWithTooLongHostname(): void
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
                    'name' => 'Test Ping Monitor',
                    'type' => 'ping',
                    'details' => [
                        'hostname' => str_repeat('a', 501).'.com', // Exceeds 500 limit
                        'packetSize' => 64,
                    ],
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function createPingMonitorWithMissingPacketSize(): void
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
                    'name' => 'Test Ping Monitor',
                    'type' => 'ping',
                    'details' => [
                        'hostname' => 'google.com',
                    ],
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function createPingMonitorWithPacketSizeTooSmall(): void
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
                    'name' => 'Test Ping Monitor',
                    'type' => 'ping',
                    'details' => [
                        'hostname' => 'google.com',
                        'packetSize' => 31, // Below minimum of 32
                    ],
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function createPingMonitorWithPacketSizeTooLarge(): void
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
                    'name' => 'Test Ping Monitor',
                    'type' => 'ping',
                    'details' => [
                        'hostname' => 'google.com',
                        'packetSize' => 65536, // Above maximum of 65535
                    ],
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function createPingMonitorWithInvalidPacketSizeType(): void
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
                    'name' => 'Test Ping Monitor',
                    'type' => 'ping',
                    'details' => [
                        'hostname' => 'google.com',
                        'packetSize' => 'invalid', // Should be integer
                    ],
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function createPingMonitorWithoutAuthentication(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $client->request(
            'POST',
            $urlGenerator->generate('api_app_monitors_store'),
            [
                'json' => [
                    'name' => 'Test Ping Monitor',
                    'type' => 'ping',
                    'details' => [
                        'hostname' => 'google.com',
                        'packetSize' => 64,
                    ],
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(401);
    }

    #[Test]
    public function createPingMonitorWithInvalidName(): void
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
                    'type' => 'ping',
                    'details' => [
                        'hostname' => 'google.com',
                        'packetSize' => 64,
                    ],
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function createPingMonitorWithMissingName(): void
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
                    'type' => 'ping',
                    'details' => [
                        'hostname' => 'google.com',
                        'packetSize' => 64,
                    ],
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function createPingMonitorWithInvalidInterval(): void
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
                    'name' => 'Test Ping Monitor',
                    'interval' => 1, // Invalid: below minimum of 3
                    'type' => 'ping',
                    'details' => [
                        'hostname' => 'google.com',
                        'packetSize' => 64,
                    ],
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function createPingMonitorWithInvalidRetryInterval(): void
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
                    'name' => 'Test Ping Monitor',
                    'retryInterval' => 2, // Invalid: below minimum of 3
                    'type' => 'ping',
                    'details' => [
                        'hostname' => 'google.com',
                        'packetSize' => 64,
                    ],
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function createPingMonitorWithInvalidRetryMax(): void
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
                    'name' => 'Test Ping Monitor',
                    'retryMax' => -1, // Invalid: below minimum of 0
                    'type' => 'ping',
                    'details' => [
                        'hostname' => 'google.com',
                        'packetSize' => 64,
                    ],
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function createPingMonitorWithInvalidType(): void
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
                    'name' => 'Test Ping Monitor',
                    'type' => 'invalid_type', // Invalid: not in allowed types
                    'details' => [
                        'hostname' => 'google.com',
                        'packetSize' => 64,
                    ],
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function createPingMonitorWithMissingType(): void
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
                    'name' => 'Test Ping Monitor',
                    'details' => [
                        'hostname' => 'google.com',
                        'packetSize' => 64,
                    ],
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function createPingMonitorWithMissingDetails(): void
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
                    'name' => 'Test Ping Monitor',
                    'type' => 'ping',
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
