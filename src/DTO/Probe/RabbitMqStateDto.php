<?php

declare(strict_types=1);

namespace App\DTO\Probe;

use Symfony\Component\Validator\Constraints as Assert;

class RabbitMqStateDto
{
    #[Assert\Type('bool')]
    public bool $connected;

    #[Assert\DateTime]
    public \DateTime $connectedAt;

    #[Assert\Type('int')]
    public int $deliveryCount;
}
