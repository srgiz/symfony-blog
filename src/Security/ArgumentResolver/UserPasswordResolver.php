<?php
declare(strict_types=1);

namespace App\Security\ArgumentResolver;

use App\Controller\AbstractDtoResolver;
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
        $dto = new UserPasswordRequest();
        $dto->oldPassword = $request->request->get('oldPassword');
        $dto->newPassword = $request->request->get('newPassword');

        return $dto;
    }
}
