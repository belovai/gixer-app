<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\Monitor\AbstractCreateMonitorDto;
use App\DTO\Monitor\CreateHttpMonitorDto;
use App\DTO\Monitor\CreateMonitorDto;
use App\DTO\Monitor\CreatePingCreateMonitorDto;
use App\Entity\Monitor;
use App\Entity\Monitors\HttpMonitor;
use App\Entity\Monitors\PingMonitor;
use App\Exception\ValidationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class MonitorService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator,
    ) {
    }

    public function createMonitor(CreateMonitorDto $monitorDto, ?AbstractCreateMonitorDto $monitorTypeDto): ?Monitor
    {
        $errors = $this->validator->validate($monitorDto);
        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }

        $errors = $this->validator->validate($monitorTypeDto);
        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }

        $monitor = new Monitor(
            name: $monitorDto->name,
            description: $monitorDto->description,
            interval: $monitorDto->interval,
            retryInterval: $monitorDto->retryInterval,
            retryMax: $monitorDto->retryMax,
        );

        $errors = $this->validator->validate($monitor);
        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }

        if ($monitorTypeDto instanceof CreatePingCreateMonitorDto) {
            $pingMonitor = new PingMonitor(
                hostname: $monitorTypeDto->hostname,
                packetSize: $monitorTypeDto->packetSize,
            );
            $errors = $this->validator->validate($pingMonitor);
            if (count($errors) > 0) {
                throw new ValidationException($errors);
            }

            $this->entityManager->persist($pingMonitor);
            $monitor->setMonitorable($pingMonitor);
        }

        if ($monitorTypeDto instanceof CreateHttpMonitorDto) {
            $httpMonitor = new HttpMonitor(
                url: $monitorTypeDto->url,
                httpMethod: $monitorTypeDto->httpMethod,
                expectedStatusCodes: $monitorTypeDto->expectedStatusCodes,
                httpBody: $monitorTypeDto->httpBody,
                httpHeaders: $monitorTypeDto->httpHeaders,
                authentication: $monitorTypeDto->authentication,
                expectedContent: $monitorTypeDto->expectedContent,
                timeout: $monitorTypeDto->timeout,
                maxRedirects: $monitorTypeDto->maxRedirects,
                upsideDown: $monitorTypeDto->upsideDown,
                ignoreSslErrors: $monitorTypeDto->ignoreSslErrors,
            );
            $errors = $this->validator->validate($httpMonitor);
            if (count($errors) > 0) {
                throw new ValidationException($errors);
            }

            $this->entityManager->persist($httpMonitor);
            $monitor->setMonitorable($httpMonitor);
        }

        $this->entityManager->persist($monitor);
        $this->entityManager->flush();

        return $monitor;
    }
}
