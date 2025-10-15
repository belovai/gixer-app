<?php

declare(strict_types=1);

namespace App\Tests\Probe;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use PHPUnit\Framework\Attributes\Test;

class DeleteProbeTest extends ApiTestCase
{
    #[Test]
    public function probeCanBeDeleted(): void
    {
    }

    #[Test]
    public function defaultProbeCannotBeDeleted(): void
    {
    }

    #[Test]
    public function deletedProbeRemovesRabbitMQResources(): void
    {
    }
}
