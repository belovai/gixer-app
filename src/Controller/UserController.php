<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\User\LoginDto;
use App\DTO\User\RegisterUserDto;
use App\Entity\Monitor;
use App\Entity\Monitors\HttpMonitor;
use App\Entity\Monitors\PingMonitor;
use App\Entity\User;
use App\Enum\HttpMethodEnum;
use App\Request\JsonRequest;
use App\Service\UserService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api', name: 'api_')]
final class UserController extends AbstractController
{
    public function __construct(
        private readonly UserService $userService,
        private readonly SerializerInterface $serializer,
        private readonly JsonRequest $jsonRequest,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/users/register', name: 'users_register', methods: ['POST'])]
    public function register(): JsonResponse
    {
        $dto = $this->jsonRequest->denormalize(RegisterUserDto::class);
        $user = $this->userService->register($dto);

        $userData = $this->serializer->serialize($user, 'json', [
            'groups' => ['user:public'],
        ]);

        return $this->json([
            'success' => true,
            'message' => 'User registered successfully',
            'data' => json_decode($userData, true),
        ], Response::HTTP_CREATED);
    }

    #[Route('/users/login', name: 'users_login', methods: ['POST'])]
    public function login(): JsonResponse
    {
        $dto = $this->jsonRequest->denormalize(LoginDto::class);
        $authResponse = $this->userService->login($dto);

        return $this->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'token' => $authResponse->token,
            ],
        ]);
    }

    #[Route('/users/logout', name: 'users_logout', methods: ['GET'])]
    public function logout(#[CurrentUser] ?User $user): JsonResponse
    {
        $this->userService->logout($user);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/users/test', name: 'users_test', methods: ['GET'])]
    public function test(): JsonResponse
    {
        $httpMonitor = new HttpMonitor('example.com', HttpMethodEnum::Get, [200]);
        $this->entityManager->persist($httpMonitor);

        $monitor = new Monitor('http monitor', '');
        $monitor->setMonitorable($httpMonitor);
        $this->entityManager->persist($monitor);

        $pingMonitor = new PingMonitor('127.0.0.1');
        $this->entityManager->persist($pingMonitor);

        $monitor2 = new Monitor('ping monitor', '');
        $monitor2->setMonitorable($pingMonitor);
        $this->entityManager->persist($monitor2);

        $this->entityManager->flush();

        //        $monitor = $this->entityManager->getRepository(Monitor::class)->find(2);
        dd($monitor, $monitor2);
    }
}
