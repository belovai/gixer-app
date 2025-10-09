<?php

namespace App\Controller;

use App\DTO\Probe\CreateProbeDto;
use App\Entity\Probe;
use App\Repository\ProbeRepository;
use App\Request\JsonRequest;
use App\Service\ProbeService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_')]
final class ProbeController extends AbstractController
{
    public function __construct(
        private readonly JsonRequest $jsonRequest,
        private readonly ProbeService $probeService,
    ) {
    }

    #[Route('/probes', name: 'app_monitors', methods: ['GET'])]
    public function index(ProbeRepository $probeRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $queryBuilder = $probeRepository->createQueryBuilder('m')->where('m.deletedAt IS NULL');

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

    #[Route('/probes/{probe:uuid}', name: 'app_probes_destroy', methods: ['DELETE'])]
    public function destroy(
        #[MapEntity(mapping: ['uuid' => 'uuid'])] Probe $probe,
    ): JsonResponse {
        $this->probeService->deleteProbe($probe);

        return $this->json([
            'success' => true,
            'message' => 'Probe deleted successfully',
        ]);
    }
}
