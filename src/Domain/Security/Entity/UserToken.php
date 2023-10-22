<?php
declare(strict_types=1);

namespace App\Domain\Security\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class UserToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'bigint')]
    public ?int $id = null;

    #[ORM\Column(name: 'user_id', type: 'integer')]
    public ?int $userId = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    public ?string $token = null;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['all'], inversedBy: 'tokens')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    public ?User $user = null;
}
