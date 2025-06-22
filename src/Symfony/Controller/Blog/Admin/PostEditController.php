<?php

declare(strict_types=1);

namespace App\Symfony\Controller\Blog\Admin;

use App\Domain\Blog\Entity\Id;
use App\Domain\Blog\UseCase\EditPost\EditPostQuery;
use App\Domain\Blog\UseCase\SavePost\SavePostModel;
use App\Domain\Blog\ViewModel\EditPostModel;
use App\Infrastructure\CommandBus\TransactionMiddleware;
use App\Symfony\Controller\AbstractController;
use App\Symfony\Form\Type\PostType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Annotation\Route;

class PostEditController extends AbstractController
{
    #[Route('/admin/blog/edit', name: 'admin-post-edit', methods: ['GET'])]
    public function edit(#[MapQueryString] EditPostQuery $query): Response
    {
        /** @var EditPostModel $post */
        $post = $this->handleCommand($query);
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
            /** @var SavePostModel $savedModel */
            $savedModel = $this->handleCommand($form->getData(), TransactionMiddleware::class);

            return $this->redirect($this->generateUrl('admin-post-edit', ['id' => $savedModel->id]));
        }

        return $this->render('blog/admin/post-edit.html.twig', ['post' => $form->getData(), 'form' => $form]);
    }
}
