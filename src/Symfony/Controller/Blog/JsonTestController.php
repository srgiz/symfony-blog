<?php

declare(strict_types=1);

namespace App\Symfony\Controller\Blog;

use SerginhoLD\JsonRpcBundle\Response\JsonRpcResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(name: 'test', methods: ['JSONRPC', 'GET'])]
#[IsGranted('ROLE_ADMIN')]
class JsonTestController
{
    public function __invoke(Request $request): JsonRpcResponse
    {
        //throw new BadRequestHttpException('fd');
        return new JsonRpcResponse(['test response']);
    }
}
