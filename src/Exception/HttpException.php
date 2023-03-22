<?php
declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException as KernelHttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class HttpException extends KernelHttpException implements ViolationExceptionInterface
{
    private ?ConstraintViolationListInterface $violations = null;

    public function getViolations(): ?ConstraintViolationListInterface
    {
        return $this->violations;
    }

    public function setViolations(ConstraintViolationListInterface $violations): static
    {
        $this->violations = $violations;
        return $this;
    }
}
