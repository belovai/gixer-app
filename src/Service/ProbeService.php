<?php
declare(strict_types=1);

namespace App\Service;

use App\DTO\Probe\CreateProbeDto;
use App\DTO\Probe\CreateProbeResponseDto;
use App\Entity\Probe;
use App\Exception\ValidationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProbeService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ValidatorInterface $validator,
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
}
