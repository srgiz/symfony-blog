<?php

declare(strict_types=1);

namespace App\Infrastructure\CommandBus;

use App\Domain\Blog\UseCase\DeletePost\DeletePostCommand;
use App\Domain\Blog\UseCase\DeletePost\DeletePostUseCase;
use App\Domain\Blog\UseCase\EditPost\EditPostQuery;
use App\Domain\Blog\UseCase\EditPost\EditPostUseCase;
use App\Domain\Blog\UseCase\GetListPublicPosts\GetListPublicPostsQuery;
use App\Domain\Blog\UseCase\GetListPublicPosts\GetListPublicPostsUseCase;
use App\Domain\Blog\UseCase\GetPublicPost\GetPublicPostQuery;
use App\Domain\Blog\UseCase\GetPublicPost\GetPublicPostUseCase;
use App\Domain\Blog\UseCase\SavePost\SavePostUseCase;
use App\Domain\Blog\UseCase\SearchPublicPosts\SearchPublicPostsQuery;
use App\Domain\Blog\UseCase\SearchPublicPosts\SearchPublicPostsUseCase;
use App\Domain\Blog\ViewModel\EditPostModel;

final class CommandBus
{
    private array $middlewares;
    private array $handlers;

    public function __construct(
        TransactionMiddleware $transactionMiddleware,
        GetListPublicPostsUseCase $getListPublicPostsUseCase,
        EditPostUseCase $editPostUseCase,
        SavePostUseCase $savePostUseCase,
        DeletePostUseCase $deletePostUseCase,
        SearchPublicPostsUseCase $searchPostsUseCase,
        GetPublicPostUseCase $getPublicPostUseCase,
    ) {
        $this->middlewares[TransactionMiddleware::class] = $transactionMiddleware;

        $this->handlers[EditPostQuery::class] = $editPostUseCase;
        $this->handlers[EditPostModel::class] = $savePostUseCase;
        $this->handlers[DeletePostCommand::class] = $deletePostUseCase;
        $this->handlers[SearchPublicPostsQuery::class] = $searchPostsUseCase;
        $this->handlers[GetPublicPostQuery::class] = $getPublicPostUseCase;
        $this->handlers[GetListPublicPostsQuery::class] = $getListPublicPostsUseCase;
    }

    /**
     * @throws \Throwable
     */
    public function __invoke(object $command, ...$middlewares): ?object
    {
        if (!isset($this->handlers[$command::class])) {
            throw new \InvalidArgumentException(sprintf('Command %s is not registered', $command::class));
        }

        $handler = $this->handlers[$command::class];

        foreach ($middlewares as $middleware) {
            if (is_string($middleware)) {
                $middleware = $this->middlewares[$middleware] ?? throw new \InvalidArgumentException(sprintf('Middleware %s is not registered', $middleware));
            }

            if (!is_callable($middleware)) {
                throw new \InvalidArgumentException(sprintf('Middleware %s is not callable', $middleware));
            }

            $handler = function (object $command) use ($middleware, $handler) {
                return $middleware($command, $handler);
            };
        }

        return $handler($command);
    }
}
