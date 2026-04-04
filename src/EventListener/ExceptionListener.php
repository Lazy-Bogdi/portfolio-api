<?php

namespace App\EventListener;

use App\Dto\Response\ErrorResponse;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

#[AsEventListener(event: KernelEvents::EXCEPTION)]
class ExceptionListener
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly string $environment,
    ) {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $request = $event->getRequest();

        if (!str_starts_with($request->getPathInfo(), '/api')) {
            return;
        }

        $exception = $event->getThrowable();

        if ($exception instanceof HttpExceptionInterface) {
            $status = $exception->getStatusCode();
            $message = $exception->getMessage();
        } else {
            $status = 500;
            $message = 'prod' === $this->environment
                ? 'Internal server error'
                : $exception->getMessage();
        }

        $error = new ErrorResponse($status, $message);
        $json = $this->serializer->serialize($error, 'json');

        $response = new JsonResponse($json, $status, [], true);
        $event->setResponse($response);
    }
}
