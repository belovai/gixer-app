<?php

namespace App\Tests\Probe;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use PHPUnit\Framework\Attributes\Test;

class ManageProbeTest extends ApiTestCase
{
    #[Test]
    public function probeCanBeMarkedAsDefault(): void
    {
    }

    #[Test]
    public function defaultProbeCannotBeDisabled(): void
    {
    }

    #[Test]
    public function disabledProbeCannotMarkAsDefault(): void
    {
    }

    #[Test]
    public function deletedProbeCannotMarkAsDefault(): void
    {
    }

    #[Test]
    public function probeCanBeDisabled(): void
    {
    }

    #[Test]
    public function probeCanBeReactivated(): void
    {
    }
}
