<?php

declare(strict_types=1);

namespace App\DTO\Monitor;

use Symfony\Component\Validator\Constraints as Assert;

class CreatePingCreateMonitorDto extends AbstractCreateMonitorDto
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 500)]
    public string $hostname;

    #[Assert\NotBlank]
    #[Assert\GreaterThanOrEqual(32)]
    #[Assert\LessThanOrEqual(65535)]
    public int $packetSize;
}
