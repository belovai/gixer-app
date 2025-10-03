<?php

namespace App\Controller;

use App\DTO\Probe\CreateProbeDto;
use App\Message\CreateRabbitMqResourcesForProbe;
use App\Message\LogMessage;
use App\Repository\MonitorRepository;
use App\Repository\ProbeRepository;
use App\Request\JsonRequest;
use App\Service\ProbeService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_')]
final class ProbeController extends AbstractController
{
    public function __construct(
        private readonly JsonRequest $jsonRequest,
        private readonly ProbeService $probeService,
        private readonly MessageBusInterface $bus,
    ) {
    }

    #[Route('/probes', name: 'app_monitors', methods: ['GET'])]
    public function index(ProbeRepository $probeRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $probeRepository->createQueryBuilder('m');

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
            context: ['groups' => 'probe:public']
        );
    }

    #[Route('/probes', name: 'app_probes_store', methods: ['POST'])]
    public function store(): JsonResponse
    {
        $createProbeDto = $this->jsonRequest->denormalize(CreateProbeDto::class);
        $probeResponse = $this->probeService->createProbe($createProbeDto);

        $this->bus->dispatch(new CreateRabbitMqResourcesForProbe(
            $probeResponse->probe->getId(),
            $probeResponse->probe->getUuid()->toRfc4122(),
            $probeResponse->plainToken
        ));

        return $this->json(
            data: [
                'success' => true,
                'message' => 'Probe created successfully',
                'data' => $probeResponse,
            ],
            status: Response::HTTP_CREATED,
            context: ['groups' => 'probe:public'],
        );
    }
}
