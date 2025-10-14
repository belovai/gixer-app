<?php

namespace App\Tests\Probe;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\ProbeFactory;
use App\Factory\UserTokenFactory;
use PHPUnit\Framework\Attributes\Test;
use Zenstruck\Foundry\Test\Factories;

class CreateProbeTest extends ApiTestCase
{
    use Factories;

    #[Test]
    public function successfulProbeCreation(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $token = bin2hex(random_bytes(32));
        UserTokenFactory::createOne([
            'token' => $token,
        ]);

        $client->request(
            'POST',
            $urlGenerator->generate('api_app_probes_store'),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'name' => 'Test Probe',
                    'enabled' => true,
                    'default' => true,
                ],
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['message' => 'Probe created successfully']);
        $this->assertJsonContains(['success' => true]);
    }

    #[Test]
    public function createProbeWithMissingData(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $token = bin2hex(random_bytes(32));
        UserTokenFactory::createOne([
            'token' => $token,
        ]);

        $client->request(
            'POST',
            $urlGenerator->generate('api_app_probes_store'),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => null,
            ]
        );

        $this->assertResponseStatusCodeSame(400);
    }

    #[Test]
    public function createProbeWithEmptyName(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $token = bin2hex(random_bytes(32));
        UserTokenFactory::createOne([
            'token' => $token,
        ]);

        $client->request(
            'POST',
            $urlGenerator->generate('api_app_probes_store'),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'name' => '',
                    'enabled' => true,
                    'default' => true,
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function createProbeWithMissingName(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $token = bin2hex(random_bytes(32));
        UserTokenFactory::createOne([
            'token' => $token,
        ]);

        $client->request(
            'POST',
            $urlGenerator->generate('api_app_probes_store'),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'enabled' => true,
                    'default' => true,
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function createProbeWithMissingEnabled(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $token = bin2hex(random_bytes(32));
        UserTokenFactory::createOne([
            'token' => $token,
        ]);

        $client->request(
            'POST',
            $urlGenerator->generate('api_app_probes_store'),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'name' => 'Test Probe',
                    'default' => true,
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function createProbeWithMissingDefault(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $token = bin2hex(random_bytes(32));
        UserTokenFactory::createOne([
            'token' => $token,
        ]);

        $client->request(
            'POST',
            $urlGenerator->generate('api_app_probes_store'),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'name' => 'Test Probe',
                    'enabled' => true,
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function createProbeWithEnabledNotBool(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $token = bin2hex(random_bytes(32));
        UserTokenFactory::createOne([
            'token' => $token,
        ]);

        $client->request(
            'POST',
            $urlGenerator->generate('api_app_probes_store'),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'name' => 'Test Probe',
                    'enabled' => 1,
                    'default' => true,
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function createProbeWithDefaultNotBool(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $token = bin2hex(random_bytes(32));
        UserTokenFactory::createOne([
            'token' => $token,
        ]);

        $client->request(
            'POST',
            $urlGenerator->generate('api_app_probes_store'),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'name' => 'Test Probe',
                    'enabled' => true,
                    'default' => 1,
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function createProbeWithTooLongName(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $token = bin2hex(random_bytes(32));
        UserTokenFactory::createOne([
            'token' => $token,
        ]);

        $client->request(
            'POST',
            $urlGenerator->generate('api_app_probes_store'),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'name' => str_repeat('a', 256), // Exceeds 255 limit
                    'enabled' => true,
                    'default' => true,
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
    }

    #[Test]
    public function createProbeWithoutAuthentication(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $client->request(
            'POST',
            $urlGenerator->generate('api_app_probes_store'),
            [
                'json' => [
                    'name' => 'Test Probe',
                    'enabled' => true,
                    'default' => true,
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(401);
    }

    #[Test]
    public function createProbeWithValidLongName(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $token = bin2hex(random_bytes(32));
        UserTokenFactory::createOne([
            'token' => $token,
        ]);

        $probeName = str_repeat('a', 255); // Exactly 255 characters (max allowed)

        $client->request(
            'POST',
            $urlGenerator->generate('api_app_probes_store'),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'name' => $probeName,
                    'enabled' => true,
                    'default' => true,
                ],
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['message' => 'Probe created successfully']);
        $this->assertJsonContains(['success' => true]);
    }

    #[Test]
    public function createProbeWithSpecialCharacters(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $token = bin2hex(random_bytes(32));
        UserTokenFactory::createOne([
            'token' => $token,
        ]);

        $probeName = 'Test Probe @#$%^&*()_+-=[]{}|;:,.<>?';

        $response = $client->request(
            'POST',
            $urlGenerator->generate('api_app_probes_store'),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'name' => $probeName,
                    'enabled' => true,
                    'default' => true,
                ],
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['message' => 'Probe created successfully']);
        $this->assertJsonContains(['success' => true]);
    }

    #[Test]
    public function firstCreatedProbeMustBeDefault(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $token = bin2hex(random_bytes(32));
        UserTokenFactory::createOne([
            'token' => $token,
        ]);

        $client->request(
            'POST',
            $urlGenerator->generate('api_app_probes_store'),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'name' => 'Test Probe',
                    'enabled' => true,
                    'default' => false,
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['success' => false]);
    }

    #[Test]
    public function firstCreatedProbeMustBeEnabled(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $token = bin2hex(random_bytes(32));
        UserTokenFactory::createOne([
            'token' => $token,
        ]);

        $client->request(
            'POST',
            $urlGenerator->generate('api_app_probes_store'),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'name' => 'Test Probe',
                    'enabled' => false,
                    'default' => true,
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['success' => false]);
    }

    #[Test]
    public function onlyOneDefaultProbeAllowed(): void
    {
        ProbeFactory::createOne([
            'default' => true,
            'enabled' => true,
        ]);

        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $token = bin2hex(random_bytes(32));
        UserTokenFactory::createOne([
            'token' => $token,
        ]);

        $client->request(
            'POST',
            $urlGenerator->generate('api_app_probes_store'),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'name' => 'Another Default',
                    'enabled' => true,
                    'default' => true,
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['success' => false]);
    }
}
