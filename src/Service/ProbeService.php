<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\Probe\CreateProbeDto;
use App\DTO\Probe\CreateProbeResponseDto;
use App\Entity\Probe;
use App\Event\ProbeCreatedEvent;
use App\Event\ProbeDeletedEvent;
use App\Exception\ValidationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

readonly class ProbeService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator,
        private EventDispatcherInterface $dispatcher,
    ) {
    }

    public function createProbe(CreateProbeDto $createProbeDto): CreateProbeResponseDto
    {
        $errors = $this->validator->validate($createProbeDto);
        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }

        $token = $this->generateToken();

        $probe = new Probe($createProbeDto, hash('sha256', $token));

        $this->entityManager->persist($probe);
        $this->entityManager->flush();

        $this->dispatcher->dispatch(
            new ProbeCreatedEvent(
                $probe->getId(),
                $probe->getUuid()->toRfc4122(),
                $token,
            ),
            'probe.created'
        );

        return new CreateProbeResponseDto(
            $probe,
            $token,
        );
    }

    public function resetToken(Probe $probe): CreateProbeResponseDto
    {
        $token = $this->generateToken();
        $probe->setToken(hash('sha256', $token));
        $this->entityManager->flush();

        return new CreateProbeResponseDto(
            $probe,
            $token,
        );
    }

    private function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    public function deleteProbe(Probe $probe): void
    {
        if ($probe->isDefault()) {
            throw new BadRequestException('Default probe cannot be deleted.', code: 400);
        }

        $this->entityManager->remove($probe);
        $this->entityManager->flush();

        $this->dispatcher->dispatch(
            new ProbeDeletedEvent(
                $probe->getId(),
                $probe->getUuid()->toRfc4122(),
            ),
            'probe.deleted'
        );
    }
}
