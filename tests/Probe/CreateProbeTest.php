<?php

declare(strict_types=1);

namespace App\Tests\Probe;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Factory\ProbeFactory;
use App\Tests\HelpersTrait;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Routing\RouterInterface;
use Zenstruck\Foundry\Test\Factories;

class CreateProbeTest extends ApiTestCase
{
    use Factories;
    use HelpersTrait;

    private Client $client;
    private RouterInterface $urlGenerator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->urlGenerator = $this->client->getContainer()->get('router');
    }

    #[Test]
    public function successfulProbeCreation(): void
    {
        $token = $this->userToken();

        $this->client->request(
            'POST',
            $this->urlGenerator->generate('api_app_probes_store'),
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
        $token = $this->userToken();

        $this->client->request(
            'POST',
            $this->urlGenerator->generate('api_app_probes_store'),
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
        $token = $this->userToken();

        $this->client->request(
            'POST',
            $this->urlGenerator->generate('api_app_probes_store'),
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
        $token = $this->userToken();

        $this->client->request(
            'POST',
            $this->urlGenerator->generate('api_app_probes_store'),
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
        $token = $this->userToken();

        $this->client->request(
            'POST',
            $this->urlGenerator->generate('api_app_probes_store'),
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
        $token = $this->userToken();

        $this->client->request(
            'POST',
            $this->urlGenerator->generate('api_app_probes_store'),
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
        $token = $this->userToken();

        $this->client->request(
            'POST',
            $this->urlGenerator->generate('api_app_probes_store'),
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
        $token = $this->userToken();

        $this->client->request(
            'POST',
            $this->urlGenerator->generate('api_app_probes_store'),
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
        $token = $this->userToken();

        $this->client->request(
            'POST',
            $this->urlGenerator->generate('api_app_probes_store'),
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
        $this->client->request(
            'POST',
            $this->urlGenerator->generate('api_app_probes_store'),
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
        $token = $this->userToken();

        $probeName = str_repeat('a', 255); // Exactly 255 characters (max allowed)

        $this->client->request(
            'POST',
            $this->urlGenerator->generate('api_app_probes_store'),
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
        $token = $this->userToken();

        $probeName = 'Test Probe @#$%^&*()_+-=[]{}|;:,.<>?';

        $this->client->request(
            'POST',
            $this->urlGenerator->generate('api_app_probes_store'),
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
        $token = $this->userToken();

        $this->client->request(
            'POST',
            $this->urlGenerator->generate('api_app_probes_store'),
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
    public function firstCreatedProbeMustBeDefaultEvenIfThereIsDeletedDefault(): void
    {
        $probe = ProbeFactory::createOne([
            'default' => true,
            'enabled' => true,
        ]);

        $entityManager = static::getContainer()->get('doctrine')->getManager();
        $entityManager->remove($probe); // Soft deletes the probe
        $entityManager->flush();

        $token = $this->userToken();

        $this->client->request(
            'POST',
            $this->urlGenerator->generate('api_app_probes_store'),
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
        $token = $this->userToken();

        $this->client->request(
            'POST',
            $this->urlGenerator->generate('api_app_probes_store'),
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

        $token = $this->userToken();

        $this->client->request(
            'POST',
            $this->urlGenerator->generate('api_app_probes_store'),
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
