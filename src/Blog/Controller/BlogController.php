<?php
declare(strict_types=1);

namespace App\Blog\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'blog', methods: ['GET'])]
class BlogController extends AbstractController
{
    public function __invoke(): Response
    {
        return new Response();
    }
}
