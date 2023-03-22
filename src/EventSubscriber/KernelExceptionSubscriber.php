<?php
declare(strict_types=1);

namespace App\EventSubscriber;

use App\Exception\ViolationExceptionInterface;
use App\Response\JsonResponseDto;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class KernelExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        #[Autowire('%kernel.environment%')] private readonly string $kernelEnvironment,
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
            $message = Response::$statusTexts[$statusCode];

        } else if ('prod' === $this->kernelEnvironment) {
            $message = Response::$statusTexts[$statusCode];
        }

        $errors = $exception instanceof ViolationExceptionInterface ? $this->formatViolations($exception->getViolations()) : null;
        $data = [];

        if ($errors) {
            $data['errors'] = $errors;
        }

        $json = $this->serializer->serialize(new JsonResponseDto($data, $statusCode), 'json', [
            'json_encode_options' => JsonResponseDto::ENCODING_OPTIONS,
        ]);

        $event->setResponse(new JsonResponse($json, $statusCode, $headers, true));
    }

    private function formatViolations(?ConstraintViolationListInterface $violations): ?array
    {
        if (!$violations) {
            return null;
        }

        $errors = [];

        foreach ($violations as $error) {
            $errors[$error->getPropertyPath()][] = $error->getMessage();
        }

        return $errors;
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
