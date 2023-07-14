<?php
declare(strict_types=1);

namespace App\Markdown\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Extra\Markdown\MarkdownInterface;

#[Route('/admin/markdown', name: 'markdown', methods: ['POST'])]
class MarkdownController extends AbstractController
{
    private MarkdownInterface $markdown;

    public function __construct(
        #[Autowire(service: 'twig.markdown.default')] MarkdownInterface $markdown,
    ) {
        $this->markdown = $markdown;
    }

    public function __invoke(Request $request): JsonResponse
    {
        return $this->json([
            'content' => $this->markdown->convert($request->getPayload()->getString('content')),
        ]);
    }
}
