<?php

namespace App\Entity\Monitors;

use App\Entity\Monitorable;
use App\Repository\Monitors\PingMonitorRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: PingMonitorRepository::class)]
class PingMonitor extends Monitorable
{
    use TimestampableEntity;

    #[ORM\Column(length: 500)]
    #[Groups(['monitor:public'])]
    private string $hostname;

    #[ORM\Column(type: 'smallint', options: ['unsigned' => true])]
    #[Groups(['monitor:public'])]
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
