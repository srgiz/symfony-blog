<?php
declare(strict_types=1);

namespace App\Dto\Request\Security;

use App\Doctrine\Validator\Constraints\UniqueEntity;
use App\Entity\User\User;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity(fields: ['email'], entityClass: User::class)]
class UserRegisterRequest
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email = null;

    #[Assert\NotBlank]
    public ?string $password = null;
}
