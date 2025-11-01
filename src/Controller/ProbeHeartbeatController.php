<?php

namespace App\Controller;

use App\DTO\Probe\HeartbeatProbeDto;
use App\Request\JsonRequest;
use App\Service\ProbeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/probes/heartbeat', name: 'api_probe_heartbeat', methods: ['POST'])]
final class ProbeHeartbeatController extends AbstractController
{
    public function __construct(
        private readonly JsonRequest $jsonRequest,
        private readonly ProbeService $probeService,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $dto = $this->jsonRequest->denormalize(HeartbeatProbeDto::class);
        /** @var \App\Entity\Probe $probe */
        $probe = $this->getUser();
        $this->probeService->heartbeat($probe, $dto);

        return $this->json(
            data: [
                'success' => true,
                'actions' => [],
            ]
        );
    }
}
