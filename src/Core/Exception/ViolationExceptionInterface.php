<?php
namespace App\Core\Exception;

use Symfony\Component\Validator\ConstraintViolationListInterface;

interface ViolationExceptionInterface
{
    public function getViolations(): ?ConstraintViolationListInterface;

    public function setViolations(ConstraintViolationListInterface $violations): static;
}
