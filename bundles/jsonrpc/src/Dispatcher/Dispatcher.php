<?php

declare(strict_types=1);

namespace SerginhoLD\JsonRpcBundle\Dispatcher;

use SerginhoLD\JsonRpcBundle\Exception\JsonRpcException;
use SerginhoLD\JsonRpcBundle\Response\JsonRpcResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @template TPayload of array{jsonrpc: string, method: string, params: array|null, id: int|string|null}
 */
readonly class Dispatcher implements DispatcherInterface
{
    public function __construct(
        private KernelInterface $kernel,
        private RouterInterface $router,
        private ErrorHandlerInterface $errorHandler,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $json = $request->getPayload()->all();$json = ['jsonrpc' => '2.0', 'method' => 'test', 'id' => 'f'];
            $isList = true;
            $responses = [];

            if (!$json) {
                throw new JsonRpcException('Invalid Request', -32600);
            }

            if (!isset($json[0]['jsonrpc'])) {
                $json = [$json];
                $isList = false;
            }

            /** @psalm-suppress MixedAssignment */
            foreach ($json as $payload) {
                $response = $this->forward($request, $payload);

                if (null !== $response->getId() || $response->error) {
                    $responses[] = $response;
                }
            }

            $headers = $this->getResponsesHeaders($responses);
            return $isList ? new JsonResponse($responses, headers: $headers) : new JsonResponse(current($responses) ?: null, headers: $headers);
        } catch (\Throwable $exception) {
            return new JsonResponse($this->errorHandler->handle($exception));
        }
    }

    private function forward(Request $request, mixed $payload): JsonRpcResponse
    {
        try {
            if (!is_array($payload)) {
                throw new JsonRpcException('Invalid Request', -32600);
            }

            /** @var TPayload $payload */
            $id = $this->getPayloadId($payload);
            $this->validatePayload($payload);
            $route = $this->router->getRouteCollection()->get($payload['method']);

            if (!$route) {
                throw new JsonRpcException('Method not found', -32601);
            }

            $response = $this->kernel->handle(
                $request->duplicate(null, $payload, [
                    '_controller' => $route->getDefault('_controller'),
                ]),
                HttpKernelInterface::SUB_REQUEST,
                //false
            );

            if (!$response instanceof JsonRpcResponse) {
                $response = $this->errorHandler->handle($response);
            }
        } catch (\Throwable $exception) {
            $response = $this->errorHandler->handle($exception);
        }

        return $response->setId($id ?? null);
    }

    /**
     * @throws JsonRpcException
     */
    private function getPayloadId(array $payload): int|string|null
    {
        /** @psalm-suppress MixedAssignment */
        $id = $payload['id'] ?? null;

        if (null === $id || is_int($id) || is_string($id)) {
            return $id;
        }

        throw new JsonRpcException('Invalid Request', -32600);
    }

    /**
     * @throws JsonRpcException
     */
    private function validatePayload(array $payload): void
    {
        /** @psalm-suppress MixedAssignment */
        $params = $payload['params'] ?? null;
        $validParams = null === $params || is_array($params);
        $validVersion = isset($payload['jsonrpc']) && '2.0' === $payload['jsonrpc'];
        $validMethod = is_string($payload['method'] ?? null);

        if (!$validParams || !$validVersion || !$validMethod) {
            throw new JsonRpcException('Invalid Request', -32600);
        }
    }

    /**
     * @param iterable<JsonRpcResponse> $responses
     * @return array
     */
    private function getResponsesHeaders(iterable $responses): array
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
