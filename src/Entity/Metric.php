<?php

namespace App\Entity;

use App\Enum\MetricStatusEnum;
use App\Repository\MetricRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: MetricRepository::class)]
#[ORM\Index(columns: ['status'])]
#[ORM\Index(columns: ['status', 'scheduled_at'])]
#[ORM\Index(columns: ['executed_at'])]
#[ORM\UniqueConstraint(columns: ['uuid', 'executed_at'])]
class Metric
{
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[Groups(['probe:public'])]
    private Uuid $uuid;

    #[ORM\ManyToOne(inversedBy: 'metrics')]
    #[ORM\JoinColumn(nullable: false)]
    private Monitor $monitor;

    #[ORM\ManyToOne(inversedBy: 'metrics')]
    #[ORM\JoinColumn(nullable: false)]
    private Probe $probe;

    #[ORM\Column(length: 255)]
    private MetricStatusEnum $status;

    #[ORM\Column(type: 'json', nullable: true, options: ['jsonb' => true])]
    private ?array $metricsData = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $scheduledAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $queuedAt = null;

    #[ORM\Id]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $executedAt = null;

    public function __construct(
        MetricStatusEnum $status,
        \DateTimeInterface $scheduledAt,
    ) {
        $this->uuid = Uuid::v4();
        $this->status = $status;
        $this->scheduledAt = $scheduledAt;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getMonitor(): ?Monitor
    {
        return $this->monitor;
    }

    public function setMonitor(?Monitor $monitor): static
    {
        $this->monitor = $monitor;

        return $this;
    }

    public function getProbe(): ?Probe
    {
        return $this->probe;
    }

    public function setProbe(?Probe $probe): static
    {
        $this->probe = $probe;

        return $this;
    }

    public function getMetricsData(): ?array
    {
        return $this->metricsData;
    }

    public function setMetricsData(?array $metrics_data): static
    {
        $this->metricsData = $metrics_data;

        return $this;
    }

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function setUuid(Uuid $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getStatus(): ?MetricStatusEnum
    {
        return $this->status;
    }

    public function setStatus(MetricStatusEnum $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getScheduledAt(): ?\DateTimeInterface
    {
        return $this->scheduledAt;
    }

    public function setScheduledAt(\DateTimeInterface $scheduledAt): static
    {
        $this->scheduledAt = $scheduledAt;

        return $this;
    }

    public function getQueuedAt(): ?\DateTimeInterface
    {
        return $this->queuedAt;
    }

    public function setQueuedAt(?\DateTimeInterface $queuedAt): static
    {
        $this->queuedAt = $queuedAt;

        return $this;
    }

    public function getExecutedAt(): ?\DateTimeInterface
    {
        return $this->executedAt;
    }

    public function setExecutedAt(?\DateTimeInterface $executedAt): static
    {
        $this->executedAt = $executedAt;

        return $this;
    }
}
