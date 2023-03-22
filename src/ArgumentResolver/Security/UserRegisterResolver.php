<?php
declare(strict_types=1);

namespace App\ArgumentResolver\Security;

use App\Controller\AbstractDtoResolver;
use App\Dto\Request\Security\UserRegisterRequest;
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
        $dto = new UserRegisterRequest();
        $dto->email = $request->request->get('email');
        $dto->password = $request->request->get('password');

        return $dto;
    }
}

