<?php
declare(strict_types=1);

namespace App\ArgumentResolver\Security;

use App\Dto\Request\Security\UserPasswordRequest;
use App\Exception\HttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class UserPasswordResolver extends AbstractResolver
{
    protected function getClassName(): string
    {
        return UserPasswordRequest::class;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $userRequest = new UserPasswordRequest();
        $userRequest->oldPassword = $request->request->get('oldPassword');
        $userRequest->newPassword = $request->request->get('newPassword');

        $errors = $this->validator->validate($userRequest);

        if ($errors->count()) {
            throw (new HttpException(400))->setDataValidatorErrors($errors);
        }

        yield $userRequest;
    }
}
