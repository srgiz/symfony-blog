<?php

declare(strict_types=1);

namespace App\Domain\Blog\UseCase\SavePost;

use App\Domain\Blog\Entity\Id;
use App\Domain\Blog\Entity\Post;
use App\Domain\Blog\Repository\PostRepositoryInterface;
use App\Domain\Blog\ViewModel\EditPostModel;
use Psr\EventDispatcher\EventDispatcherInterface;

readonly class SavePostUseCase
{
    public function __construct(
        private PostRepositoryInterface $postRepository,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function __invoke(EditPostModel $command): SavePostModel
    {
        $post = new Post(
            id: $command->id ?? new Id(),
            status: Post\Status::from($command->status),
            title: $command->title,
            content: $command->content,
            preview: $command->preview,
        );

        $this->postRepository->save($post);
        $this->eventDispatcher->dispatch(new SavePostEvent($post));

        return new SavePostModel($post->getId());
    }
}
