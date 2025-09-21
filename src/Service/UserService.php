<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\User\AuthResponseDto;
use App\DTO\User\LoginDto;
use App\DTO\User\RegisterUserDto;
use App\Entity\User;
use App\Exception\ValidationException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly ValidatorInterface $validator,
        private readonly UserTokenService $tokenService,
    ) {
    }

    public function register(RegisterUserDto $dto): User
    {
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }

        $user = new User(
            email: $dto->email,
            timezone: $dto->timezone ?? 'UTC',
            locale: $dto->locale ?? 'en'
        );
        $user->setPassword($this->passwordHasher->hashPassword($user, $dto->password));

        $errors = $this->validator->validate($user);

        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function login(LoginDto $dto): AuthResponseDto
    {
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }

        $user = $this->userRepository->findOneBy(['email' => $dto->email]);
        if (!$user || !$this->passwordHasher->isPasswordValid($user, $dto->password)) {
            throw new BadRequestHttpException('Invalid credentials.');
        }

        if (!$user->isEnabled()) {
            throw new BadRequestHttpException('User account is disabled.');
        }

        $token = $this->tokenService->generateAuthToken($user);

        return new AuthResponseDto($token);
    }

    public function logout(?User $user): int
    {
        if (!$user) {
            return 0;
        }

        return $this->tokenService->revokeAllTokens($user);
    }
}
