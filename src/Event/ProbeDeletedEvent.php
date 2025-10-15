<?php

declare(strict_types=1);

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ProbeDeletedEvent extends Event
{
    public function __construct(
        private readonly int $probeId,
        private readonly string $probeUuid,
    ) {
    }

    public function getProbeId(): int
    {
        return $this->probeId;
    }

    public function getProbeUuid(): string
    {
        return $this->probeUuid;
    }
}
