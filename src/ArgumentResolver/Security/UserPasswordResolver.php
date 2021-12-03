<?php
declare(strict_types=1);

namespace App\ArgumentResolver\Security;

use App\Dto\Request\Security\UserPasswordRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class UserPasswordResolver extends AbstractResolver
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
