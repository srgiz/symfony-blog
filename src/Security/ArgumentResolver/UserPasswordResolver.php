<?php
declare(strict_types=1);

namespace App\Security\ArgumentResolver;

use App\Core\Controller\AbstractDtoResolver;
use App\Security\Dto\Request\UserPasswordRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class UserPasswordResolver extends AbstractDtoResolver
{
    protected function getClassName(): string
    {
        return UserPasswordRequest::class;
    }

    protected function createRequestDto(Request $request, ArgumentMetadata $argument): object
    {
        return new UserPasswordRequest(
            (string)$request->request->get('oldPassword'),
            (string)$request->request->get('newPassword'),
        );
    }
}
