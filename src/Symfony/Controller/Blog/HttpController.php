<?php

declare(strict_types=1);

namespace App\Symfony\Controller\Blog;

//use App\Core\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/http', name: 'http', methods: ['GET'])]
class HttpController extends AbstractController
{
    public function __invoke(/*#[Autowire(service: 'http_client.github')] HttpClientInterface $client*/): JsonResponse
    {
        /*
        $response = $client->request('GET', '737628064502.json', ['json' => ['a' => 0], 'headers' => ['x-a' => 'c']
        //,'save_to' => 'nw','sink' => 'da'
        ]);
        var_dump($response);*/

        return new JsonResponse(['http2']);
    }
}
