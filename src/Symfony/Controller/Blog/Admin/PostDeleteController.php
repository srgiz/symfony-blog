<?php

declare(strict_types=1);

namespace App\Symfony\Controller\Blog\Admin;

use App\Domain\Blog\Entity\Id;
use App\Domain\Blog\UseCase\DeletePost\DeletePostCommand;
use App\Domain\Blog\UseCase\DeletePost\DeletePostUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/blog/delete', name: 'admin-post-delete', methods: ['POST'])]
class PostDeleteController extends AbstractController
{
    public function __construct(
        private readonly DeletePostUseCase $useCase,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        ($this->useCase)(new DeletePostCommand(new Id($request->getPayload()->get('id'))));

        return $this->redirect($this->generateUrl('admin-post-list'));
    }
}
