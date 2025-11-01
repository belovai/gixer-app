<?php

declare(strict_types=1);

namespace App\DTO\Probe;

use Symfony\Component\Validator\Constraints as Assert;

class AppStateDto
{
    #[Assert\NotBlank]
    #[Assert\Regex('/^\d+\.\d+\.\d+(-\w+)?$/')]
    public string $version;

    #[Assert\NotBlank]
    #[Assert\DateTime]
    public \DateTime $startupAt;
}
