<?php

declare(strict_types=1);

namespace App\Symfony\Controller\Blog\Admin;

use App\Core\Blog\Service\PostManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/blog/delete', name: 'admin-post-delete', methods: ['POST'])]
class PostDeleteController extends AbstractController
{
    public function __construct(private readonly PostManager $manager)
    {
    }

    public function __invoke(Request $request): Response
    {
        $this->manager->deleteById(
            $request->getPayload()->getInt('id'),
        );

        return $this->redirect($this->generateUrl('admin-post-list'));
    }
}
