<?php

namespace App\Controller;

use App\DTO\Monitor\CreateHttpMonitorDto;
use App\DTO\Monitor\CreateMonitorDto;
use App\DTO\Monitor\CreatePingCreateMonitorDto;
use App\Request\JsonRequest;
use App\Service\MonitorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api', name: 'api_')]
final class MonitorController extends AbstractController
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly JsonRequest $jsonRequest,
        private readonly EntityManagerInterface $entityManager,
        private readonly MonitorService $monitorService,
    ) {
    }

    #[Route('/monitors', name: 'app_monitors', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/MonitorController.php',
        ]);
    }

    #[Route('/monitors', name: 'app_monitors_store', methods: ['POST'])]
    public function store(): JsonResponse
    {
        $monitorDto = $this->jsonRequest->denormalize(CreateMonitorDto::class);

        $monitorTypeDto = match ($monitorDto->type) {
            'ping' => $this->serializer->denormalize($monitorDto->details, CreatePingCreateMonitorDto::class),
            'http' => $this->serializer->denormalize($monitorDto->details, CreateHttpMonitorDto::class),
            default => null,
        };

        $monitor = $this->monitorService->createMonitor($monitorDto, $monitorTypeDto);

        $monitorData = $this->serializer->serialize($monitor, 'json', [
            'groups' => ['monitor:public'],
        ]);

        return $this->json([
            'success' => true,
            'message' => 'Monitor created successfully',
            'data' => json_decode($monitorData, true),
        ], Response::HTTP_CREATED);
    }
}
