<?php
declare(strict_types=1);

namespace App\Security\Dto\Request;

use App\Core\Doctrine\Validator\Constraints\UniqueEntity;
use App\Security\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueEntity(fields: ['email'], entityClass: User::class)]
readonly class UserRegisterRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,
        #[Assert\NotBlank]
        public string $password,
    ) {}
}
