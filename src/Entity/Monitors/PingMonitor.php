<?php

namespace App\Entity\Monitors;

use App\Entity\Monitorable;
use App\Repository\Monitors\PingMonitorRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

#[ORM\Entity(repositoryClass: PingMonitorRepository::class)]
class PingMonitor extends Monitorable
{
    use TimestampableEntity;

    #[ORM\Column(length: 500)]
    private string $hostname;

    #[ORM\Column(type: 'smallint', options: ['unsigned' => true])]
    private int $packetSize;

    public function __construct(
        string $hostname,
        int $packetSize = 56,
    ) {
        $this->hostname = $hostname;
        $this->packetSize = $packetSize;
    }

    public function getHostname(): ?string
    {
        return $this->hostname;
    }

    public function setHostname(string $hostname): static
    {
        $this->hostname = $hostname;

        return $this;
    }

    public function getPacketSize(): ?int
    {
        return $this->packetSize;
    }

    public function setPacketSize(int $packetSize): static
    {
        $this->packetSize = $packetSize;

        return $this;
    }
}
