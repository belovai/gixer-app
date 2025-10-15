<?php

namespace App\Message;

use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
final readonly class DeleteRabbitMqResourcesForProbe
{
    public function __construct(
        private string $username,
    ) {
    }

    public function getUsername(): string
    {
        return $this->username;
    }
}
