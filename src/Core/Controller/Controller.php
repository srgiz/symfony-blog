<?php
declare(strict_types=1);

namespace App\Core\Controller;

use App\Core\Dto\Response\JsonResponseDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse as KernelJsonResponse;

abstract class Controller extends AbstractController
{
    protected function json(mixed $data, int $status = 200, array $headers = [], array $context = []): KernelJsonResponse
    {
        return parent::json($data, $status, $headers, array_merge([
            'json_encode_options' => JsonResponseDto::ENCODING_OPTIONS,
        ], $context));
    }
}
