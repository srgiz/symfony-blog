<?php
declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException as KernelHttpException;
use Symfony\Component\Validator;

class HttpException extends KernelHttpException implements DataExceptionInterface
{
    private ?array $data;

    public function __construct(
        int $statusCode,
        ?string $message = null,
        array $data = null,
        \Throwable $previous = null,
        array $headers = [],
        ?int $code = null
    ) {
        parent::__construct(
            $statusCode,
            $message ?? Response::$statusTexts[$statusCode],
            $previous,
            $headers,
            $code ?? $statusCode
        );

        $this->data = $data;
    }

    public function getData(): ?array
    {
        return $this->data;
    }

    public function setData(?array $data): static
    {
        $this->data = $data;
        return $this;
    }

    public function setDataValidatorErrors(Validator\ConstraintViolationListInterface $errors): static
    {
        $data = [];

        /** @var Validator\ConstraintViolationInterface $error */
        foreach ($errors as $error) {
            $data[$error->getPropertyPath()][] = $error->getMessage();
        }

        return $this->setData($data);
    }
}
