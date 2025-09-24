<?php
declare(strict_types=1);

namespace App\DTO\Probe;

use App\Entity\Probe;
use Symfony\Component\Serializer\Annotation\Groups;

class CreateProbeResponseDto
{
    public function __construct(
        #[Groups(['probe:public'])] public Probe $probe,
        #[Groups(['probe:public'])] public string $plainToken,
    ) {
    }
}
