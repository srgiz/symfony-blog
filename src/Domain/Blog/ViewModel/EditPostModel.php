<?php

declare(strict_types=1);

namespace App\Domain\Blog\ViewModel;

use App\Domain\Blog\Entity\Id;
use App\Domain\Blog\Entity\Post\Status;

class EditPostModel
{
    public string $status = Status::Draft->value;

    public ?string $title = null;

    public ?string $preview = null;

    public ?string $content = null;

    public function __construct(
        public ?Id $id = null,
    ) {
    }
}
