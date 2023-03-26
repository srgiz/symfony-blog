<?php
declare(strict_types=1);

namespace App\Catalog\Controller;

use App\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/', name: 'index', methods: ['GET'])]
class IndexAction extends Controller
{
    public function __invoke(): Response
    {
        return $this->render('default/index.html.twig');
    }
}
