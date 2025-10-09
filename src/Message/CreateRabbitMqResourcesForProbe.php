<?php

declare(strict_types=1);

namespace App\Message;

final readonly class CreateRabbitMqResourcesForProbe
{
    public function __construct(
        private int $probeId,
        private string $username,
        private string $password,
    ) {
    }

    public function getProbeId(): int
    {
        return $this->probeId;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
