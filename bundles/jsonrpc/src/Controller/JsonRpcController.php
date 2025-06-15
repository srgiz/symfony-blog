<?php

declare(strict_types=1);

namespace Srgiz\JsonRpcBundle\Controller;

use Psr\Log\LoggerInterface;
use Srgiz\JsonRpcBundle\Exception\JsonRpcException;
use Srgiz\JsonRpcBundle\Exception\JsonRpcResponseException;
use Srgiz\JsonRpcBundle\Request\Payload;
use Srgiz\JsonRpcBundle\Response\JsonRpcResponse;
use Srgiz\JsonRpcBundle\Serializer\NativeSerializer;
use Srgiz\JsonRpcBundle\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\RouterInterface;

readonly class JsonRpcController
{
    public function __construct(
        private LoggerInterface $logger,
        private RouterInterface $router,
        private KernelInterface $kernel,
        private SerializerInterface $serializer = new NativeSerializer(),
        private string $routePrefix = '',
        private int $maxRequests = 1,
        private bool $catch = false,
    ) {
    }

    final public function __invoke(Request $request): JsonResponse
    {
        try {
            $json = $request->getPayload()->all();
            $json = [['jsonrpc' => '2.0', 'method' => 'test', 'id' => 'f'], ['jsonrpc' => '2.0', 'method' => 'test2', 'id' => 'f2']];

            if (!$json) {
                throw new JsonRpcException('Invalid Request', -32600);
            }

            /** @var array[] $json */
            if (!($isList = array_is_list($json))) {
                $json = [$json];
            }

            $countRequests = 0;
            $responses = [];

            foreach ($json as $item) {
                ++$countRequests;

                try {
                    $payload = Payload::create($item);
                } catch (\TypeError $typeError) {
                    $responses[] = $this->createErrorResponse(new JsonRpcException('Parse error', -32700, previous: $typeError));
                    continue;
                }

                if ($countRequests > $this->maxRequests) {
                    $responses[] = $this->createErrorResponse(
                        new JsonRpcException(sprintf('Only %u requests per batch are allowed', $this->maxRequests), -32500),
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

            if (!$responses) {
                return new JsonResponse($isList ? [] : null);
            }

            return $this->json($isList ? $responses : current($responses));
        } catch (JsonRpcException $exception) {
            return $this->json($this->createErrorResponse($exception));
        } catch (\Throwable $exception) {
            return $this->json($this->createErrorResponse(new JsonRpcException('Server error', -32000, previous: $exception)));
        }
    }

    private function request(Request $request, Payload $payload): JsonRpcResponse
    {
        try {
            if ('2.0' !== $payload->jsonrpc) {
                throw new JsonRpcException('Invalid Request', -32600);
            }

            $route = $this->router->getRouteCollection()->get($this->routePrefix.$payload->method);

            /** @var string|null $controller */
            $controller = $route?->getDefault('_controller');

            if (
                !$route
                || !in_array('JSONRPC', $route->getMethods(), true)
                || $request->attributes->get('_controller') == $controller // loop
            ) {
                throw new JsonRpcException('Method not found', -32601);
            }

            $subRequest = $request->duplicate(attributes: [
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
        $context = array_filter((array) $payload, fn ($value) => null !== $value);
        $context['exception'] = $exception;
        $this->logger->error($exception->getMessage(), $context);

        return match (true) {
            $exception instanceof JsonRpcException => JsonRpcResponse::fromError($exception->getCode(), $exception->getMessage(), $exception->getData()),
            default => $this->createExceptionResponse($exception),
        };
    }

    protected function createExceptionResponse(\Throwable $exception): JsonRpcResponse
    {
        return JsonRpcResponse::fromError(-32603, 'Internal error');
    }

    private function json(array|JsonRpcResponse $responses): JsonResponse
    {
        return new JsonResponse(
            $this->serializer->serialize($responses),
            headers: $this->getHeaders($responses),
            json: true
        );
    }

    private function getHeaders(array|JsonRpcResponse $responses): array
    {
        /** @var JsonRpcResponse[] $responses */
        $responses = is_array($responses) ? $responses : [$responses];
        $headers = [];

        foreach ($responses as $response) {
            foreach ($response->headers as $key => $value) {
                $headers[$key] = $value;
            }
        }

        return $headers;
    }
}
