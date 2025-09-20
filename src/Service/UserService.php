<?php
declare(strict_types=1);

namespace App\Service;

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
        private readonly ValidatorInterface $validator
    ) {
        //
    }

    public function register(RegisterUserDto $dto): User
    {
        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            throw new ValidationException($errors);
        }
//
//        if ($this->userRepository->findOneBy(['email' => $dto->email])) {
//            throw new BadRequestHttpException('Email address is already registered.');
//        }

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
}
