<?php
declare(strict_types=1);

namespace App\Controller;

use App\DTO\User\RegisterUserDto;
use App\Request\JsonRequest;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api', name: 'api_')]
final class UserController extends AbstractController
{

    public function __construct(
        private readonly UserService $userService,
        private readonly SerializerInterface $serializer,
        private readonly JsonRequest $jsonRequest
    ) {
        //
    }

    #[Route('/users', name: 'users_register', methods: ['POST'])]
    public function register(): JsonResponse
    {
        $dto = $this->jsonRequest->denormalize(RegisterUserDto::class);
        $user = $this->userService->register($dto);

        $userData = $this->serializer->serialize($user, 'json', [
            'groups' => ['user:public']
        ]);

        return $this->json([
            'success' => true,
            'message' => 'User registered successfully',
            'data' => json_decode($userData, true)
        ], Response::HTTP_CREATED);
    }
}
