<?php

declare(strict_types=1);

namespace SerginhoLD\JsonRpcBundle\Controller;

use SerginhoLD\JsonRpcBundle\Exception\JsonRpcException;
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
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsController]
readonly class JsonRpcController
{
    public function __construct(
        private ValidatorInterface $validator,
        private KernelInterface $kernel,
        private RouterInterface $router,
        private string $routePrefix = '',
        private int $maxRequests = 10,
        private bool $catch = false,
    ) {}

    public function __invoke(Request $request): JsonResponse
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
                if (++$countRequests > $this->maxRequests) {
                    $responses[] = $this->createErrorResponse(new JsonRpcException(sprintf('Only %u requests per batch are allowed', $this->maxRequests), 429));
                    continue;
                }

                $response = $this->request($request, $payload = Payload::create($item));

                if (null !== $payload->id) {
                    // response|notification
                    $responses[] = $response->setId($payload->id);
                }
            }

            return new JsonResponse($isList ? $responses : (current($responses) ?: null), headers: $this->getResponsesHeaders($responses));
        } catch (\Throwable $exception) {
            return new JsonResponse($this->createErrorResponse($exception));
        }
    }

    private function request(Request $request, Payload $payload): JsonRpcResponse
    {
        try {
            $errors = $this->validator->validate($payload);

            if (count($errors)) {
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

            return $this->createErrorResponse($response);
        } catch (\Throwable $exception) {
            return $this->createErrorResponse($exception);
        }
    }

    /**
     * @param JsonRpcResponse[] $responses
     */
    private function getResponsesHeaders(array $responses): array
    {
        $headers = [];

        foreach ($responses as $response) {
            foreach ($response->headers as $key => $value) {
                $headers[$key] = $value;
            }
        }

        return $headers;
    }

    private function createErrorResponse(\Throwable|Response $error): JsonRpcResponse
    {
        if ($error instanceof Response) {
            if ($error->getStatusCode() >= 400 && isset(Response::$statusTexts[$error->getStatusCode()])) {
                return JsonRpcResponse::fromError($error->getStatusCode(), Response::$statusTexts[$error->getStatusCode()]);
            }

            return JsonRpcResponse::fromError(-32603, 'Internal error');
        }

        return match (true) {
            $error instanceof JsonRpcException => JsonRpcResponse::fromError($error->getCode(), $error->getMessage()),
            $error instanceof HttpExceptionInterface => JsonRpcResponse::fromError($error->getStatusCode(), $error->getMessage()),
            $error instanceof AccessDeniedException => JsonRpcResponse::fromError(401, 'Unauthorized'),
            default => JsonRpcResponse::fromError(-32603, 'Internal error'),
        };
    }
}
