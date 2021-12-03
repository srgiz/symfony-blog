<?php
declare(strict_types=1);

namespace App\Controller;

use App\Dto\Response\ResponseDtoInterface;
use App\Response\ResponseSerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

abstract class Controller extends AbstractController
{
    protected function json($data, int $status = 200, array $headers = [], array $context = []): JsonResponse
    {
        if ($data instanceof ResponseDtoInterface) {
            return $this->container->get('response.json')->serialize($data, $status, $headers, $context);
        }

        return parent::json($data, $status, $headers, $context);
    }

    public static function getSubscribedServices(): array
    {
        $services = parent::getSubscribedServices();
        $services['response.json'] = ResponseSerializerInterface::class;
        return $services;
    }
}
