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

class ManageProbeTest extends ApiTestCase
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
    public function probeCanBeSetAsDefault(): void
    {
        $defaultProbe = ProbeFactory::createOne([
            'default' => true,
            'enabled' => true,
        ]);

        $probe = ProbeFactory::createOne([
            'default' => false,
            'enabled' => true,
        ]);

        $token = $this->userToken();

        $this->client->request(
            'PATCH',
            $this->urlGenerator->generate('api_app_probes_update', ['probe' => $probe->getUuid()]),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'default' => true,
                ],
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['success' => true]);

        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $entityManager->refresh($defaultProbe);
        $this->assertFalse($defaultProbe->isDefault());
    }

    #[Test]
    public function defaultProbeMustBeEnabled(): void
    {
        $probe = ProbeFactory::createOne([
            'default' => false,
            'enabled' => false,
        ]);

        $token = $this->userToken();

        $this->client->request(
            'PATCH',
            $this->urlGenerator->generate('api_app_probes_update', ['probe' => $probe->getUuid()]),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'default' => true,
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['success' => false]);
    }

    #[Test]
    public function defaultProbeCannotBeDisabled(): void
    {
        $probe = ProbeFactory::createOne([
            'default' => true,
            'enabled' => true,
        ]);

        $token = $this->userToken();

        $this->client->request(
            'PATCH',
            $this->urlGenerator->generate('api_app_probes_update', ['probe' => $probe->getUuid()]),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'enabled' => false,
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['success' => false]);
    }

    #[Test]
    public function disabledProbeCannotMarkAsDefault(): void
    {
        $probe = ProbeFactory::createOne([
            'default' => false,
            'enabled' => false,
        ]);

        $token = $this->userToken();

        $this->client->request(
            'PATCH',
            $this->urlGenerator->generate('api_app_probes_update', ['probe' => $probe->getUuid()]),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'default' => true,
                ],
            ]
        );

        $this->assertResponseStatusCodeSame(422);
        $this->assertJsonContains(['success' => false]);
    }

    #[Test]
    public function probeCanBeDisabled(): void
    {
        $probe = ProbeFactory::createOne([
            'default' => false,
            'enabled' => true,
        ]);

        $token = $this->userToken();

        $this->client->request(
            'PATCH',
            $this->urlGenerator->generate('api_app_probes_update', ['probe' => $probe->getUuid()]),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'enabled' => false,
                ],
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['success' => true]);

        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $entityManager->refresh($probe);
        $this->assertFalse($probe->isEnabled());
    }

    #[Test]
    public function probeCanBeReactivated(): void
    {
        $probe = ProbeFactory::createOne([
            'default' => false,
            'enabled' => false,
        ]);

        $token = $this->userToken();

        $this->client->request(
            'PATCH',
            $this->urlGenerator->generate('api_app_probes_update', ['probe' => $probe->getUuid()]),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'enabled' => true,
                ],
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['success' => true]);

        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $entityManager->refresh($probe);
        $this->assertTrue($probe->isEnabled());
    }

    #[Test]
    public function probeNameCanBeUpdated(): void
    {
        $probe = ProbeFactory::createOne([
            'name' => 'Old Name',
        ]);

        $token = $this->userToken();

        $this->client->request(
            'PATCH',
            $this->urlGenerator->generate('api_app_probes_update', ['probe' => $probe->getUuid()]),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
                'json' => [
                    'name' => 'New Name',
                ],
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains(['success' => true]);

        $entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $entityManager->refresh($probe);
        $this->assertEquals('New Name', $probe->getName());
    }
}
