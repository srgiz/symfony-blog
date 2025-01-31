<?php

declare(strict_types=1);

namespace App\Symfony\Controller\Blog\Admin;

use App\Domain\Blog\Entity\Id;
use App\Domain\Blog\UseCase\EditPost\EditPostQuery;
use App\Domain\Blog\UseCase\EditPost\EditPostUseCase;
use App\Domain\Blog\UseCase\SavePost\SavePostUseCase;
use App\Domain\Blog\ViewModel\EditPostModel;
use App\Symfony\Form\Type\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Annotation\Route;

class PostEditController extends AbstractController
{
    public function __construct(
        private readonly EditPostUseCase $editPostUseCase,
        private readonly SavePostUseCase $savePostUseCase,
    ) {
    }

    #[Route('/admin/blog/edit', name: 'admin-post-edit', methods: ['GET'])]
    public function edit(#[MapQueryString] EditPostQuery $query): Response
    {
        $post = ($this->editPostUseCase)($query);
        $form = $this->createForm(PostType::class, $post);

        return $this->render('blog/admin/post-edit.html.twig', ['post' => $post, 'form' => $form]);
    }

    #[Route('/admin/blog/edit', name: 'admin-post-save', methods: ['POST'])]
    public function save(Request $request): Response
    {
        $id = $request->get('id');
        $id = $id ? new Id($id) : null;
        $form = $this->createForm(PostType::class, new EditPostModel(id: $id));
        $form->handleRequest($request);

        if ($form->isValid()) {
            ($this->savePostUseCase)($post = $form->getData());

            return $this->redirect($this->generateUrl('admin-post-edit', ['id' => $post->id]));
        }

        return $this->render('blog/admin/post-edit.html.twig', ['post' => $form->getData(), 'form' => $form]);
    }
}
