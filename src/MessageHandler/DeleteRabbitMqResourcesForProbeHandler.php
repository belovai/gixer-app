<?php

namespace App\MessageHandler;

use App\Message\DeleteRabbitMqResourcesForProbe;
use App\Service\RabbitMqAdminService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class DeleteRabbitMqResourcesForProbeHandler
{
    public function __construct(
        private RabbitMqAdminService $rabbitMqAdmin,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(DeleteRabbitMqResourcesForProbe $message): void
    {
        $this->logger->info('Processing message to delete RabbitMQ resources for probe.', [
            'username' => $message->getUsername(),
        ]);

        try {
            $this->rabbitMqAdmin->deleteQueue($message->getUsername());
            $this->rabbitMqAdmin->deleteUser($message->getUsername());
            $this->logger->info('Successfully deleted RabbitMQ resources.');
        } catch (\Exception $e) {
            $this->logger->error('Failed to delete RabbitMQ resources.', [
                'exception' => $e->getMessage(),
                'probeUuid' => $message->getUsername(),
            ]);

            throw $e;
        }
    }
}
