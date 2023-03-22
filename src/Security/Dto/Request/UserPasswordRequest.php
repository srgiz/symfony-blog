<?php
declare(strict_types=1);

namespace App\Security\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

class UserPasswordRequest
{
    #[Assert\NotBlank]
    public ?string $oldPassword = null;

    #[Assert\NotBlank]
    public ?string $newPassword = null;
}
