<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\CreateRabbitMqResourcesForProbe;
use App\Service\RabbitMqAdminService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CreateRabbitMqResourcesForProbeHandler
{
    public function __construct(
        private RabbitMqAdminService $rabbitMqAdmin,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(CreateRabbitMqResourcesForProbe $message): void
    {
        $this->logger->info('Processing message to create RabbitMQ resources for probe.', [
            'probeId' => $message->getProbeId(),
            'username' => $message->getUsername(),
        ]);

        try {
            $this->rabbitMqAdmin->createUser($message->getUsername(), $message->getPassword());
            $this->rabbitMqAdmin->createQueue($message->getUsername());
            $this->logger->info('Successfully created RabbitMQ resources.');
        } catch (\Exception $e) {
            $this->logger->error('Failed to create RabbitMQ resources.', [
                'exception' => $e->getMessage(),
                'probeId' => $message->getProbeId(),
            ]);

            throw $e;
        }
    }
}
