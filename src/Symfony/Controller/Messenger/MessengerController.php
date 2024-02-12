<?php

declare(strict_types=1);

namespace App\Symfony\Controller\Messenger;

use App\Core\Messenger\MessageManager;
use App\Core\Utils\PaginatorUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/messenger', name: 'messenger', methods: ['GET'])]
class MessengerController extends AbstractController
{
    public function __construct(
        private readonly MessageManager $manager,
    ) {
    }

    public function __invoke(#[MapQueryParameter] string $page = '1'): Response
    {
        return $this->render('messenger/messenger.html.twig', $this->manager->paginate(PaginatorUtils::page($page)));
    }
}
