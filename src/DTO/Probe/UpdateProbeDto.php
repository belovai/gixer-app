<?php

declare(strict_types=1);

namespace App\DTO\Probe;

use App\Validator\DefaultImpliesEnabled;
use Symfony\Component\Validator\Constraints as Assert;

#[DefaultImpliesEnabled]
class UpdateProbeDto
{
    #[Assert\Length(max: 255)]
    public string $name;

    #[Assert\Type('bool')]
    public bool $enabled;

    #[Assert\Type('bool')]
    public bool $default;
}
