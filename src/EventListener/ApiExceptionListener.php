<?php

declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Serializer\Exception\MissingConstructorArgumentsException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;

class ApiExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $statusCode = 500;
        $message = 'An unexpected error occurred';

        match (true) {
            $exception instanceof MissingConstructorArgumentsException => [
                $statusCode = 422, // Unprocessable Entity
                $message = sprintf('Request body is missing required parameter(s): %s', $exception->getMissingConstructorArguments()[0] ?? 'unknown'),
            ],
            $exception instanceof NotNormalizableValueException => [
                $statusCode = 422, // Unprocessable Entity
                $message = sprintf('The type of the "%s" attribute is not valid. Expected "%s", but "%s" given.', $exception->getPath(), implode('|', (array) $exception->getExpectedTypes()), $exception->getCurrentType()),
            ],
            $exception instanceof \InvalidArgumentException && str_contains($exception->getMessage(), 'Invalid JSON data') => [
                $statusCode = 400, // Bad Request
                $message = 'The request body contains malformed JSON.',
            ],
            default => null,
        };

        if ($statusCode === 500) {
            return;
        }

        $response = new JsonResponse([
            'success' => false,
            'message' => $message,
        ], $statusCode);

        $event->setResponse($response);
    }
}
