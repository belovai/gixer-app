<?php
declare(strict_types=1);

namespace App\EventListener;

use App\Exception\InvalidTokenException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class BadRequestExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if (!$exception instanceof BadRequestHttpException && !$exception instanceof InvalidTokenException) {
            return;
        }

        $response = new JsonResponse([
            'success' => false,
            'message' => $exception->getMessage(),
        ], 400);

        $event->setResponse($response);
    }
}
