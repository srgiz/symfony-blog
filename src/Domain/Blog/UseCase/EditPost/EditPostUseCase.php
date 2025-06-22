<?php

declare(strict_types=1);

namespace App\Domain\Blog\UseCase\EditPost;

use App\Domain\Blog\Repository\PostRepositoryInterface;
use App\Domain\Blog\ViewModel\EditPostModel;

readonly class EditPostUseCase
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
    ) {
    }

    public function __invoke(EditPostQuery $command): EditPostModel
    {
        $dto = new EditPostModel();

        if ($post = $command->id ? $this->postRepository->find($command->id) : null) {
            $dto->id = $post->getId();
            $dto->status = $post->getStatus()->value;
            $dto->title = $post->getTitle();
            $dto->preview = $post->getPreview();
            $dto->content = $post->getContent();
        }

        return $dto;
    }
}
