<?php
declare(strict_types=1);

namespace App\Dto\Request\Security;

use Symfony\Component\Validator\Constraints as Assert;

##[UniqueEntity(fields: ['email'], em: 'default', entityClass: User::class)]
// todo. сделать свой валидатор, этот кривой какой-то, обязательно вешать на сущность
// use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
class UserRegisterRequest
{
    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email = null;

    #[Assert\NotBlank]
    public ?string $password = null;
}
