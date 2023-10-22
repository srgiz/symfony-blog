<?php
declare(strict_types=1);

namespace App\Domain\Blog\Service;

use App\Domain\Blog\Entity\Post;
use App\Domain\Blog\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class PostManager
{
    private PostRepository $postRepository;

    public function __construct(
        private EntityManagerInterface $em,
    ) {
        /** @psalm-suppress PropertyTypeCoercion */
        $this->postRepository = $em->getRepository(Post::class);
    }

    public function paginate(int $page, int $limit = 1): array
    {
        $page = max($page, 1);
        $offset = intval(($page - 1) * $limit);
        $blog = $this->postRepository->paginateAll($offset, $limit);

        return [
            'page' => $page,
            'blog' => $blog,
            'totalPages' => (int)ceil($blog->count() / $limit),
        ];
    }

    public function getById(int $id): ?Post
    {
        return $this->postRepository->findOneBy(['id' => $id]);
    }

    public function edit(Request $request, FormInterface $form): bool
    {
        $form->handleRequest($request);

        /** @var Post $post */
        $post = $form->getData();

        if (!$form->isValid()) {
            return false;
        }

        if (!$this->em->getUnitOfWork()->isInIdentityMap($post)) {
            $this->em->persist($post);
        }

        $this->em->flush();
        return true;
    }

    public function deleteById(int $id): void
    {
        if ($post = $this->em->getReference(Post::class, $id)) {
            $this->em->remove($post);
            $this->em->flush();
        }
    }
}
