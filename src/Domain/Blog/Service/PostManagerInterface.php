<?php

namespace App\Domain\Blog\Service;

use App\Domain\Blog\Entity\Post;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

interface PostManagerInterface
{
    public function paginate(int $page, int $limit = 1): array;

    public function getById(int $id): ?Post;

    // todo: вынести
    public function edit(Request $request, FormInterface $form): bool;

    public function deleteById(int $id): void;
}
