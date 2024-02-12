<?php

declare(strict_types=1);

namespace App\Symfony\Controller\Blog;

use SerginhoLD\JsonRpcBundle\Request\Payload;
use SerginhoLD\JsonRpcBundle\Response\JsonRpcError;
use SerginhoLD\JsonRpcBundle\Response\JsonRpcResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Attribute\SerializedName;

#[Route('/jsonrpc/test', name: 'test', methods: ['JSONRPC', 'GET'])]
#[IsGranted('ROLE_ADMIN')]
class JsonTestController
{
    public function __invoke(Payload $payload): JsonRpcResponse
    {
        // throw new BadRequestHttpException('fd');
        return new JsonRpcResponse([new TestDto('A', 'b')]/* , new JsonRpcError(1, 's') */);
    }
}

readonly class TestDto
{
    public function __construct(
        #[SerializedName('foo_c')]
        public string $foo,
        public string $bar,
    ) {
    }
}
