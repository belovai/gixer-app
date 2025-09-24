<?php

namespace App\Controller;

use App\DTO\Monitor\CreateHttpMonitorDto;
use App\DTO\Monitor\CreateMonitorDto;
use App\DTO\Monitor\CreatePingCreateMonitorDto;
use App\Entity\Monitor;
use App\Repository\MonitorRepository;
use App\Request\JsonRequest;
use App\Service\MonitorService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
    public function index(MonitorRepository $monitorRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $monitorRepository->createQueryBuilder('m');

        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $pagination = $paginator->paginate(
            $queryBuilder,
            $page,
            $limit
        );

        $data = [
            'success' => true,
            'data' => $pagination->getItems(),
            'pagination' => [
                'total_items' => $pagination->getTotalItemCount(),
                'items_per_page' => $pagination->getItemNumberPerPage(),
                'current_page' => $pagination->getCurrentPageNumber(),
                'total_pages' => ceil($pagination->getTotalItemCount() / $pagination->getItemNumberPerPage()),
            ],
        ];

        return $this->json(
            data: $data,
            context: ['groups' => 'monitor:public']
        );
    }

    #[Route('/monitors/{monitor:uuid}', name: 'app_monitors_show', methods: ['GET'])]
    public function show(
        #[MapEntity(mapping: ['uuid' => 'uuid'])] Monitor $monitor,
    ): JsonResponse {
        $data = [
            'success' => true,
            'data' => $monitor,
        ];

        return $this->json(
            data: $data,
            context: ['groups' => 'monitor:public']
        );
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

        $data = [
            'success' => true,
            'message' => 'Monitor created successfully',
            'data' => $monitor,
        ];

        return $this->json(
            data: $data,
            status: Response::HTTP_CREATED,
            context: ['groups' => 'monitor:public'],
        );
    }
}
