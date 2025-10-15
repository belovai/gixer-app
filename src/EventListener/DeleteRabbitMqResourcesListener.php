<?php

namespace App\EventListener;

use App\Event\ProbeDeletedEvent;
use App\Message\DeleteRabbitMqResourcesForProbe;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Messenger\MessageBusInterface;

final class DeleteRabbitMqResourcesListener
{
    public function __construct(private MessageBusInterface $bus)
    {
    }

    #[AsEventListener(event: 'probe.deleted')]
    public function onProbeDeleted(ProbeDeletedEvent $event): void
    {
        $this->bus->dispatch(new DeleteRabbitMqResourcesForProbe($event->getProbeUuid()));
    }
}
