<?php

declare(strict_types=1);

namespace App\DTO\Monitor;

use App\Enum\HttpMethodEnum;
use App\Validator\ValidAuthentication;
use App\Validator\ValidHeaders;
use App\Validator\ValidStatusCodes;
use Symfony\Component\Validator\Constraints as Assert;

class CreateHttpMonitorDto extends AbstractCreateMonitorDto
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 500)]
    #[Assert\Url]
    public string $url;

    #[Assert\NotBlank]
    public HttpMethodEnum $httpMethod;

    #[Assert\NotBlank]
    #[Assert\Type('array')]
    #[ValidStatusCodes]
    public array $expectedStatusCodes;

    #[Assert\Length(max: 5000)]
    public ?string $httpBody = null;

    #[Assert\Type('array')]
    #[ValidHeaders]
    public ?array $httpHeaders = null;

    #[Assert\Type('array')]
    #[ValidAuthentication]
    public ?array $authentication = null;

    #[Assert\Length(max: 5000)]
    public ?string $expectedContent = null;

    #[Assert\GreaterThanOrEqual(1)]
    #[Assert\LessThanOrEqual(3600)]
    public int $timeout = 10;

    #[Assert\GreaterThanOrEqual(0)]
    #[Assert\LessThanOrEqual(30)]
    public int $maxRedirects = 10;

    #[Assert\Type('bool')]
    public bool $upsideDown = false;

    #[Assert\Type('bool')]
    public bool $ignoreSslErrors = false;
}
