<?php
declare(strict_types=1);

namespace App\Markdown\Controller;

use App\Markdown\MarkdownInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/markdown', name: 'markdown', methods: ['POST'])]
class MarkdownController extends AbstractController
{
    public function __construct(private MarkdownInterface $markdown) {}

    public function __invoke(Request $request): JsonResponse
    {
        return $this->json([
            'content' => $this->markdown->parse($request->getPayload()->getString('content')),
        ]);
    }
}
