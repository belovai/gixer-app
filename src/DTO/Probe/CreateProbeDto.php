<?php
declare(strict_types=1);

namespace App\DTO\Probe;

use Symfony\Component\Validator\Constraints as Assert;

class CreateProbeDto
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    public string $name;

    #[Assert\NotNull]
    #[Assert\Type('bool')]
    public bool $enabled;

    #[Assert\NotNull]
    #[Assert\Type('bool')]
    public bool $default;
}
