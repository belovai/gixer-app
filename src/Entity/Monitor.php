<?php

namespace App\Entity;

use App\Repository\MonitorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MonitorRepository::class)]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt', timeAware: false, hardDelete: true)]
class Monitor
{
    use SoftDeleteableEntity;
    use TimestampableEntity;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(type: UuidType::NAME, length: 36, unique: true)]
    #[Groups(['monitor:public'])]
    private string $uuid;

    #[ORM\Column(length: 255)]
    #[Groups(['monitor:public'])]
    private string $name;

    #[ORM\Column(type: 'text')]
    #[Groups(['monitor:public'])]
    private string $description;

    #[ORM\Column(options: ['default' => 'true'])]
    #[Groups(['monitor:public'])]
    private bool $enabled = true;

    #[ORM\ManyToOne(targetEntity: Monitorable::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['monitor:public'])]
    private ?Monitorable $monitorable = null;

    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    #[Groups(['monitor:public'])]
    private int $interval;

    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    #[Groups(['monitor:public'])]
    private int $retryInterval;

    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    #[Groups(['monitor:public'])]
    private int $retryMax;

    /**
     * @var Collection<int, Metric>
     */
    #[ORM\OneToMany(targetEntity: Metric::class, mappedBy: 'monitor')]
    private Collection $metrics;

    public function __construct(
        string $name,
        string $description,
        int $interval = 60,
        int $retryInterval = 60,
        int $retryMax = 2,
    ) {
        $this->uuid = $uuid ?? Uuid::uuid4()->toString();
        $this->name = $name;
        $this->description = $description;
        $this->interval = $interval;
        $this->retryInterval = $retryInterval;
        $this->retryMax = $retryMax;
        $this->metrics = new ArrayCollection();
    }

    /**
     * @return string[]
     */
    public static function monitorTypes(): array
    {
        return [
            'http',
            'ping',
        ];
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

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getMonitorable(): ?Monitorable
    {
        return $this->monitorable;
    }

    public function setMonitorable(?Monitorable $monitorable): static
    {
        $this->monitorable = $monitorable;

        return $this;
    }

    public function getInterval(): ?int
    {
        return $this->interval;
    }

    public function setInterval(int $interval): static
    {
        $this->interval = $interval;

        return $this;
    }

    public function getRetryInterval(): ?int
    {
        return $this->retryInterval;
    }

    public function setRetryInterval(int $retryInterval): static
    {
        $this->retryInterval = $retryInterval;

        return $this;
    }

    public function getRetryMax(): ?int
    {
        return $this->retryMax;
    }

    public function setRetryMax(int $retryMax): static
    {
        $this->retryMax = $retryMax;

        return $this;
    }

    /**
     * @return Collection<int, Metric>
     */
    public function getMetrics(): Collection
    {
        return $this->metrics;
    }

    public function addMetric(Metric $metric): static
    {
        if (!$this->metrics->contains($metric)) {
            $this->metrics->add($metric);
            $metric->setMonitor($this);
        }

        return $this;
    }

    public function removeMetric(Metric $metric): static
    {
        if ($this->metrics->removeElement($metric)) {
            // set the owning side to null (unless already changed)
            if ($metric->getMonitor() === $this) {
                $metric->setMonitor(null);
            }
        }

        return $this;
    }
}
