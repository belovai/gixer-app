<?php

declare(strict_types=1);

namespace App\Event;

use Symfony\Contracts\EventDispatcher\Event;

class ProbeCreatedEvent extends Event
{
    public function __construct(
        private readonly int $probeId,
        private readonly string $probeUuid,
        private readonly string $plainToken,
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

    public function getPlainToken(): string
    {
        return $this->plainToken;
    }
}
