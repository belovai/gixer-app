<?php

namespace App\Tests\Probe;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Factory\UserFactory;
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

        $user = UserFactory::createOne();
        $token = $this->loginUser($client, $user->getEmail());

        $response = $client->request(
            'POST',
            $urlGenerator->generate('api_app_probes_store'),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'name' => 'Test Probe',
                ],
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['message' => 'Probe created successfully']);
        $this->assertJsonContains(['success' => true]);
    }

    #[Test]
    public function createProbeWithMissingName(): void
    {
        $client = static::createClient();
        $urlGenerator = $client->getContainer()->get('router');

        $user = UserFactory::createOne();
        $token = $this->loginUser($client, $user->getEmail());

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

        $user = UserFactory::createOne();
        $token = $this->loginUser($client, $user->getEmail());

        $client->request(
            'POST',
            $urlGenerator->generate('api_app_probes_store'),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'name' => '',
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

        $user = UserFactory::createOne();
        $token = $this->loginUser($client, $user->getEmail());

        $client->request(
            'POST',
            $urlGenerator->generate('api_app_probes_store'),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'name' => str_repeat('a', 256), // Exceeds 255 limit
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

        $user = UserFactory::createOne();
        $token = $this->loginUser($client, $user->getEmail());

        $probeName = str_repeat('a', 255); // Exactly 255 characters (max allowed)

        $response = $client->request(
            'POST',
            $urlGenerator->generate('api_app_probes_store'),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'name' => $probeName,
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

        $user = UserFactory::createOne();
        $token = $this->loginUser($client, $user->getEmail());

        $probeName = 'Test Probe @#$%^&*()_+-=[]{}|;:,.<>?';

        $response = $client->request(
            'POST',
            $urlGenerator->generate('api_app_probes_store'),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'name' => $probeName,
                ],
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['message' => 'Probe created successfully']);
        $this->assertJsonContains(['success' => true]);
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
