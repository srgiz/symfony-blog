<?php
declare(strict_types=1);

namespace App\ArgumentResolver\Security;

use App\Exception\HttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractResolver implements ArgumentValueResolverInterface
{
    protected ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return $argument->getType() === $this->getClassName();
    }

    abstract protected function getClassName(): string;

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $dto = $this->createRequestDto($request, $argument);

        $errors = $this->validator->validate($dto);

        if ($errors->count()) {
            throw (new HttpException(400))->setDataValidatorErrors($errors);
        }

        yield $dto;
    }

    abstract protected function createRequestDto(Request $request, ArgumentMetadata $argument): object;
}
