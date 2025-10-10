<?php

declare(strict_types=1);

namespace App\DTO\Probe;

use App\Validator\FirstProbeIsDefault;
use App\Validator\FirstProbeIsEnabled;
use Symfony\Component\Validator\Constraints as Assert;

class CreateProbeDto
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $name;

    #[Assert\NotNull]
    #[Assert\Type('bool')]
    #[FirstProbeIsEnabled]
    public bool $enabled;

    #[Assert\NotNull]
    #[Assert\Type('bool')]
    #[FirstProbeIsDefault]
    public bool $default;
}
