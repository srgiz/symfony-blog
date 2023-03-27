<?php
declare(strict_types=1);

namespace App\Security\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

readonly class UserPasswordRequest
{
    public function __construct(
        #[Assert\NotBlank]
        public string $oldPassword,
        #[Assert\NotBlank]
        public string $newPassword,
    ) {}
}
