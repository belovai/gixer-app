<?php

declare(strict_types=1);

namespace App\DTO\Monitor;

use App\Entity\Monitor;
use Symfony\Component\Validator\Constraints as Assert;

class CreateMonitorDto
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $name;

    public string $description = '';

    #[Assert\Type('bool')]
    public bool $enabled = true;

    #[Assert\NotBlank]
    #[Assert\GreaterThan(2)]
    #[Assert\LessThanOrEqual(2592000)] // 30 days
    public int $interval = 60;

    #[Assert\NotBlank]
    #[Assert\GreaterThan(2)]
    #[Assert\LessThanOrEqual(2592000)] // 30 days
    public int $retryInterval = 60;

    #[Assert\NotBlank]
    #[Assert\GreaterThanOrEqual(0)]
    public int $retryMax = 2;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: [Monitor::class, 'monitorTypes'])]
    public string $type;

    #[Assert\NotBlank]
    public array $details;
}
