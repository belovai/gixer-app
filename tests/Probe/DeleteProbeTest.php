<?php

declare(strict_types=1);

namespace App\Tests\Probe;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Factory\ProbeFactory;
use App\Message\DeleteRabbitMqResourcesForProbe;
use App\Tests\HelpersTrait;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;
use Symfony\Component\Routing\RouterInterface;
use Zenstruck\Foundry\Test\Factories;

class DeleteProbeTest extends ApiTestCase
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
    public function probeCanBeDeleted(): void
    {
        $probe = ProbeFactory::createOne([
            'default' => false,
            'enabled' => true,
        ]);

        $token = $this->userToken();

        $this->client->request(
            'DELETE',
            $this->urlGenerator->generate('api_app_probes_destroy', ['probe' => $probe->getUuid()]),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
            ]
        );

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['success' => true]);
    }

    #[Test]
    public function defaultProbeCannotBeDeleted(): void
    {
        $probe = ProbeFactory::createOne([
            'default' => true,
            'enabled' => true,
        ]);

        $token = $this->userToken();

        $this->client->request(
            'DELETE',
            $this->urlGenerator->generate('api_app_probes_destroy', ['probe' => $probe->getUuid()]),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
            ]
        );

        $this->assertResponseStatusCodeSame(400);
        $this->assertJsonContains(['success' => false]);
    }

    #[Test]
    public function deletingProbeRemovesRabbitMQResources(): void
    {
        $probe = ProbeFactory::createOne([
            'default' => false,
            'enabled' => true,
        ]);

        $token = $this->userToken();

        $this->client->request(
            'DELETE',
            $this->urlGenerator->generate('api_app_probes_destroy', ['probe' => $probe->getUuid()]),
            [
                'headers' => ['Authorization' => 'Bearer '.$token],
            ]
        );

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['success' => true]);

        $transport = $this->client->getContainer()->get('messenger.transport.async');
        $this->assertInstanceOf(InMemoryTransport::class, $transport);

        $envelopes = $transport->getSent();
        $this->assertCount(1, $envelopes);

        $message = $envelopes[0]->getMessage();
        $this->assertInstanceOf(DeleteRabbitMqResourcesForProbe::class, $message);
        $this->assertEquals($probe->getUuid(), $message->getUsername());
    }
}
