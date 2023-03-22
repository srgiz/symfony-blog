<?php
declare(strict_types=1);

namespace App\Controller;

use App\Exception\HttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractDtoResolver implements ValueResolverInterface
{
    public function __construct(
        protected ValidatorInterface $validator,
    ) {}

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== $this->getClassName()) {
            return [];
        }

        $dto = $this->createRequestDto($request, $argument);
        $errors = $this->validator->validate($dto);

        if ($errors->count()) {
            throw (new HttpException(400))->setDataValidatorErrors($errors);
        }

        yield $dto;
    }

    abstract protected function getClassName(): string;

    abstract protected function createRequestDto(Request $request, ArgumentMetadata $argument): object;
}
