<?php
declare(strict_types=1);

namespace App\ArgumentResolver\Security;

use App\Dto\Request\Security\UserRegisterRequest;
use App\Exception\HttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserRegisterResolver implements ArgumentValueResolverInterface
{
    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return $argument->getType() === UserRegisterRequest::class;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $userRequest = new UserRegisterRequest();
        $userRequest->email = $request->request->get('email');
        $userRequest->password = $request->request->get('password');

        $errors = $this->validator->validate($userRequest);

        if ($errors->count()) {
            throw (new HttpException(400))->setDataValidatorErrors($errors);
        }

        yield $userRequest;
    }
}

