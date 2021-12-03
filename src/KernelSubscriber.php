<?php
declare(strict_types=1);

namespace App;

use App\Dto\Response\ResponseDto;
use App\Exception\DataExceptionInterface;
use App\Response\ResponseSerializerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class KernelSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ResponseSerializerInterface $responseSerializer,
        private string $kernelEnvironment,
    ) {}

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $statusCode = $exception->getCode();
        $message = $exception->getMessage();
        $headers = [];

        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
            $headers = $exception->getHeaders();
        }

        $isClientError = $statusCode >= 400 && $statusCode < 500;

        if (!$isClientError) {
            if ('prod' !== $this->kernelEnvironment) {
                // show exception
                return;
            }

            $statusCode = 500;
            $message = 'Internal Server Error';

        } else if ('prod' === $this->kernelEnvironment) {
            $message = Response::$statusTexts[$statusCode];
        }

        $data = $exception instanceof DataExceptionInterface ? $exception->getData() : null;

        $event->setResponse(
            $this->responseSerializer->serialize(
                (new ResponseDto())->setError($statusCode, $message, $data),
                $statusCode,
                $headers,
            ),
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => [
                ['onKernelException', -100],
            ],
        ];
    }
}
