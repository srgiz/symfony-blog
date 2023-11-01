<?php
declare(strict_types=1);

namespace App\Symfony\Controller\Blog;

use App\Core\Blog\Service\PostPublicService;
use App\Core\Message\TestMessage;
use App\Core\Messenger\MessageBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/post/{slug}', name: 'post', methods: ['GET'])]
class PostController extends AbstractController
{
    public function __construct(
        private readonly PostPublicService $service,
        private readonly MessageBusInterface $messageBus,
    ) {}

    public function __invoke(string $slug): Response
    {
        $post = $this->service->getBySlug($slug);

        if (!$post) {
            throw new NotFoundHttpException();
        }

        $this->messageBus->dispatch(new TestMessage(time()));

        return $this->render('blog/post.html.twig', ['post' => $post]);
    }
}
