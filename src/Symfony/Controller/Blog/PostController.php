<?php

declare(strict_types=1);

namespace App\Symfony\Controller\Blog;

use App\Domain\Blog\Entity\Id;
use App\Domain\Blog\UseCase\GetPublicPost\GetPublicPostQuery;
use App\Domain\Blog\UseCase\GetPublicPost\GetPublicPostUseCase;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/post/{slug}', name: 'post', methods: ['GET'])]
class PostController extends AbstractController
{
    public function __construct(
        private readonly GetPublicPostUseCase $useCase,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function __invoke(string $slug): Response
    {
        $this->logger->debug('Test debug', [
            'token' => '#abc~',
            'user' => ['name' => 'Ivan', 'id' => 4],
            'mask' => ['user.name', 'token'],
            'exception' => new \Exception('Oops'),
        ]);

        try {
            $query = new GetPublicPostQuery(new Id($slug));
        } catch (\InvalidArgumentException $e) {
            throw new NotFoundHttpException(previous: $e);
        }

        $post = ($this->useCase)($query);

        if (!$post) {
            throw new NotFoundHttpException();
        }

        return $this->render('blog/post.html.twig', ['post' => $post]);
    }
}
