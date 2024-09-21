<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table('user_token')]
class UserTokenData
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'bigint')]
    public int|string|null $id = null;

    #[ORM\Column(name: 'user_id', type: 'integer')]
    public ?int $userId = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    public ?string $token = null;

    #[ORM\ManyToOne(targetEntity: UserData::class, cascade: ['all'], inversedBy: 'tokens')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    public ?UserData $user = null;
}
