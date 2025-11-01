<?php

declare(strict_types=1);

namespace App\DTO\Probe;

class HeartbeatProbeDto
{
    public AppStateDto $appState;

    public RabbitMqStateDto $rabbitMqState;
}
