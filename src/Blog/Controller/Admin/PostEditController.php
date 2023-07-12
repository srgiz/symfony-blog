<?php
declare(strict_types=1);

namespace App\Blog\Controller\Admin;

use App\Blog\Entity\Post;
use App\Blog\Form\Type\PostType;
use App\Blog\Service\PostManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/blog/edit', name: 'admin-post-edit', methods: ['GET', 'POST'])]
class PostEditController extends AbstractController
{
    public function __construct(private readonly PostManager $manager) {}

    public function __invoke(Request $request, #[MapQueryParameter] string $id = ''): Response
    {
        $post = $this->manager->getById((int)$id) ?? new Post();
        $form = $this->createForm(PostType::class, $post);

        if ($request->isMethod('POST') && $this->manager->edit($request, $form)) {
            return $this->redirect($this->generateUrl('admin-post-edit', ['id' => $post->id]));
        }

        return $this->render('blog/admin/post-edit.html.twig', ['post' => $post, 'form' => $form]);
    }
}
