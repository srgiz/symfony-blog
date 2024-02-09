<?php

declare(strict_types=1);

namespace SerginhoLD\JsonRpcBundle\Controller;

use Psr\Log\LoggerInterface;
use SerginhoLD\JsonRpcBundle\Exception\JsonRpcException;
use SerginhoLD\JsonRpcBundle\Exception\JsonRpcResponseException;
use SerginhoLD\JsonRpcBundle\Request\Payload;
use SerginhoLD\JsonRpcBundle\Response\JsonRpcResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\SerializerInterface;

#[AsController]
readonly class JsonRpcController
{
    public function __construct(
        protected SerializerInterface $serializer,
        protected LoggerInterface $logger,
        private RouterInterface $router,
        private KernelInterface $kernel,
        private string $routePrefix = '',
        private int $maxRequests = 1,
        private bool $catch = false,
    ) {}

    final public function __invoke(Request $request): JsonResponse
    {
        try {
            $json = $request->getPayload()->all();$json = [['jsonrpc' => '2.0', 'method' => 'test', 'id' => 'f'],['jsonrpc' => '2.0', 'method' => 'test2', 'id' => 'f2']];

            if (!$json) {
                throw new JsonRpcException('Invalid Request', -32600);
            }

            if (!($isList = array_is_list($json))) {
                $json = [$json];
            }

            $countRequests = 0;
            $responses = [];

            foreach ($json as $item) {
                ++$countRequests;

                try {
                    $payload = Payload::create($item);
                } catch (\TypeError) {
                    $responses[] = $this->createErrorResponse(new JsonRpcException('Parse error', -32700));
                    continue;
                }

                if ($countRequests > $this->maxRequests) {
                    $responses[] = $this->createErrorResponse(
                        new JsonRpcException(sprintf('Only %u requests per batch are allowed', $this->maxRequests), 429),
                        $payload
                    )->setId($payload->id);

                    continue;
                }

                $response = $this->request($request, $payload);

                if (null !== $payload->id) {
                    // response|notification
                    $responses[] = $response->setId($payload->id);
                }
            }

            return $this->json($isList, $responses);
        } catch (\Throwable $exception) {
            return $this->json(false, [$this->createErrorResponse($exception)]);
        }
    }

    private function request(Request $request, Payload $payload): JsonRpcResponse
    {
        try {
            if ('2.0' !== $payload->jsonrpc) {
                throw new JsonRpcException('Invalid Request', -32600);
            }

            $route = $this->router->getRouteCollection()->get($this->routePrefix.$payload->method);
            $controller = $route?->getDefault('_controller');

            if (
                !$route
                || !in_array('JSONRPC', $route->getMethods(), true)
                || $request->attributes->get('_controller') == $controller // loop
            ) {
                throw new JsonRpcException('Method not found', -32601);
            }

            $subRequest = $request->duplicate(null, null, [
                '_controller' => $controller,
                'payload' => $payload,
            ]);

            $subRequest->setMethod('JSONRPC');

            $response = $this->kernel->handle(
                $subRequest,
                HttpKernelInterface::SUB_REQUEST,
                $this->catch
            );

            if ($response instanceof JsonRpcResponse) {
                return $response;
            }

            throw new JsonRpcResponseException($response);
        } catch (\Throwable $exception) {
            return $this->createErrorResponse($exception, $payload);
        }
    }

    private function createErrorResponse(\Throwable $exception, ?Payload $payload = null): JsonRpcResponse
    {
        $context = array_filter((array) $payload, fn($value) => null !== $value);
        $context['exception'] = $exception;
        $this->logger->error($exception->getMessage(), $context);

        return match (true) {
            $exception instanceof JsonRpcException => JsonRpcResponse::fromError($exception->getCode(), $exception->getMessage(), $exception->getData()),
            default => $this->createExceptionResponse($exception),
        };
    }

    protected function createExceptionResponse(\Throwable $exception): JsonRpcResponse
    {
        switch (true) {
            case $exception instanceof JsonRpcResponseException:
                $code = $exception->getResponse()->getStatusCode();
                [$code, $message] = $code >= 400 && isset(Response::$statusTexts[$code]) ? [$code, Response::$statusTexts[$code]] : [-32603, 'Internal error'];
                return JsonRpcResponse::fromError($code, $message);

            case $exception instanceof HttpExceptionInterface:
                return JsonRpcResponse::fromError($exception->getStatusCode(), $exception->getMessage());

            case $exception instanceof AccessDeniedException:
                return JsonRpcResponse::fromError(401, 'Unauthorized');

            default:
                return JsonRpcResponse::fromError(-32603, 'Internal error');
        }
    }

    /**
     * @param JsonRpcResponse[] $responses
     */
    protected function json(bool $isList, array $responses): JsonResponse
    {
        if (!$responses) {
            return new JsonResponse($isList ? [] : null);
        }

        return new JsonResponse(
            $this->serializer->serialize($isList ? $responses : current($responses), 'json'),
            headers: $this->getHeaders($responses),
            json: true
        );
    }

    /**
     * @param JsonRpcResponse[] $responses
     */
    protected function getHeaders(array $responses): array
    {
        $headers = [];

        foreach ($responses as $response) {
            foreach ($response->headers as $key => $value) {
                $headers[$key] = $value;
            }
        }

        return $headers;
    }
}
