<?php

namespace App\EventListener;

use App\Event\ProbeCreatedEvent;
use App\Message\CreateRabbitMqResourcesForProbe;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class CreateRabbitMqResourcesListener
{
    public function __construct(private MessageBusInterface $bus)
    {
    }

    #[AsEventListener(event: 'probe.created')]
    public function onProbeCreated(ProbeCreatedEvent $event): void
    {
        $this->bus->dispatch(new CreateRabbitMqResourcesForProbe(
            $event->getProbeId(),
            $event->getProbeUuid(),
            $event->getPlainToken()
        ));
    }
}
