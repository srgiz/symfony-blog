<?php
declare(strict_types=1);

namespace App\Security\ArgumentResolver;

use App\Core\Controller\AbstractDtoResolver;
use App\Security\Dto\Request\UserRegisterRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class UserRegisterResolver extends AbstractDtoResolver
{
    protected function getClassName(): string
    {
        return UserRegisterRequest::class;
    }

    protected function createRequestDto(Request $request, ArgumentMetadata $argument): object
    {
        return new UserRegisterRequest(
            (string)$request->request->get('email'),
            (string)$request->request->get('password'),
        );
    }
}

