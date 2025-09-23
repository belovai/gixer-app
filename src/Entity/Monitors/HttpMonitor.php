<?php

namespace App\Entity\Monitors;

use App\Entity\Monitorable;
use App\Enum\HttpMethodEnum;
use App\Repository\Monitors\HttpMonitorRepository;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\Uuid;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: HttpMonitorRepository::class)]
class HttpMonitor extends Monitorable
{
    use TimestampableEntity;

    #[ORM\Column(type: UuidType::NAME, length: 36, unique: true)]
    #[Groups(['monitor:public'])]
    private string $uuid;

    #[ORM\Column(length: 500)]
    #[Groups(['monitor:public'])]
    private string $url;

    #[ORM\Column(type: 'string', enumType: HttpMethodEnum::class)]
    #[Groups(['monitor:public'])]
    private HttpMethodEnum $httpMethod;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['monitor:public'])]
    private ?string $httpBody = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['monitor:public'])]
    private ?array $httpHeaders = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['monitor:public'])]
    private ?array $authentication = null;

    #[ORM\Column(type: 'json')]
    #[Groups(['monitor:public'])]
    private array $expectedStatusCodes;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['monitor:public'])]
    private ?string $expectedContent = null;

    #[ORM\Column(type: 'integer', options: ['unsigned' => true, 'default' => '10'])]
    #[Groups(['monitor:public'])]
    private int $timeout = 10;

    #[ORM\Column(type: 'smallint', options: ['unsigned' => true, 'default' => '10'])]
    #[Groups(['monitor:public'])]
    private int $maxRedirects = 10;

    #[ORM\Column(type: 'boolean', options: ['default' => 'false'])]
    #[Groups(['monitor:public'])]
    private bool $upsideDown = false;

    #[ORM\Column(type: 'boolean', options: ['default' => 'false'])]
    #[Groups(['monitor:public'])]
    private bool $ignoreSslErrors = false;

    public function __construct(
        string $url,
        HttpMethodEnum $httpMethod,
        array $expectedStatusCodes,
        ?string $httpBody = null,
        ?array $httpHeaders = null,
        ?array $authentication = null,
        ?string $expectedContent = null,
        int $timeout = 10,
        int $maxRedirects = 10,
        bool $upsideDown = false,
        bool $ignoreSslErrors = false,
    ) {
        $this->uuid = $uuid ?? Uuid::uuid4()->toString();
        $this->url = $url;
        $this->httpMethod = $httpMethod;
        $this->expectedStatusCodes = $expectedStatusCodes;
        $this->httpBody = $httpBody;
        $this->httpHeaders = $httpHeaders;
        $this->authentication = $authentication;
        $this->expectedContent = $expectedContent;
        $this->timeout = $timeout;
        $this->maxRedirects = $maxRedirects;
        $this->upsideDown = $upsideDown;
        $this->ignoreSslErrors = $ignoreSslErrors;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getHttpMethod(): ?HttpMethodEnum
    {
        return $this->httpMethod;
    }

    public function setHttpMethod(HttpMethodEnum $httpMethod): static
    {
        $this->httpMethod = $httpMethod;

        return $this;
    }

    public function getHttpBody(): ?string
    {
        return $this->httpBody;
    }

    public function setHttpBody(?string $httpBody): static
    {
        $this->httpBody = $httpBody;

        return $this;
    }

    public function getHttpHeaders(): ?array
    {
        return $this->httpHeaders;
    }

    public function setHttpHeaders(?array $httpHeaders): static
    {
        $this->httpHeaders = $httpHeaders;

        return $this;
    }

    public function getAuthentication(): ?array
    {
        return $this->authentication;
    }

    public function setAuthentication(?array $authentication): static
    {
        $this->authentication = $authentication;

        return $this;
    }

    public function getExpectedStatusCodes(): array
    {
        return $this->expectedStatusCodes;
    }

    public function setExpectedStatusCodes(array $expectedStatusCodes): static
    {
        $this->expectedStatusCodes = $expectedStatusCodes;

        return $this;
    }

    public function getExpectedContent(): ?string
    {
        return $this->expectedContent;
    }

    public function setExpectedContent(?string $expectedContent): static
    {
        $this->expectedContent = $expectedContent;

        return $this;
    }

    public function getTimeout(): ?int
    {
        return $this->timeout;
    }

    public function setTimeout(int $timeout): static
    {
        $this->timeout = $timeout;

        return $this;
    }

    public function getMaxRedirects(): ?int
    {
        return $this->maxRedirects;
    }

    public function setMaxRedirects(int $maxRedirects): static
    {
        $this->maxRedirects = $maxRedirects;

        return $this;
    }

    public function isUpsideDown(): ?bool
    {
        return $this->upsideDown;
    }

    public function setUpsideDown(bool $upsideDown): static
    {
        $this->upsideDown = $upsideDown;

        return $this;
    }

    public function isIgnoreSslErrors(): ?bool
    {
        return $this->ignoreSslErrors;
    }

    public function setIgnoreSslErrors(bool $ignoreSslErrors): static
    {
        $this->ignoreSslErrors = $ignoreSslErrors;

        return $this;
    }
}
