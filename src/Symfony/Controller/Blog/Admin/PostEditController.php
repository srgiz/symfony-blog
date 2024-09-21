<?php

declare(strict_types=1);

namespace App\Symfony\Controller\Blog\Admin;

use App\Core\Blog\Service\PostManager;
use App\Infrastructure\Doctrine\Entity\PostData;
use App\Symfony\Attribute\MapForm;
use App\Symfony\Form\Type\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/blog/edit', name: 'admin-post-edit', methods: ['GET', 'POST'])]
class PostEditController extends AbstractController
{
    public function __construct(private readonly PostManager $manager)
    {
    }

    public function __invoke(Request $request, #[MapForm(PostType::class, PostData::class)] FormInterface $form): Response
    {
        /** @var PostData $post */
        $post = $form->getData();

        if ($request->isMethod('POST') && $form->isValid() && $this->manager->edit($post)) {
            return $this->redirect($this->generateUrl('admin-post-edit', ['id' => $post->id]));
        }

        return $this->render('blog/admin/post-edit.html.twig', ['post' => $post, 'form' => $form]);
    }
}
