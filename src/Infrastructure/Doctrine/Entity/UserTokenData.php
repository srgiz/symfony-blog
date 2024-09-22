<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Entity;

use App\Domain\Blog\Entity\Id;
use App\Infrastructure\Doctrine\DomainIdGenerator;
use App\Infrastructure\Doctrine\Types\IdType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table('user_token')]
class UserTokenData
{
    public function __construct(
        #[ORM\ManyToOne(targetEntity: UserData::class, cascade: ['all'], inversedBy: 'tokens')]
        #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
        public readonly UserData $user,
        #[ORM\Column(type: 'string', length: 255, unique: true)]
        public readonly string $token,
        #[ORM\Id]
        #[ORM\Column(type: IdType::NAME)]
        #[ORM\GeneratedValue(strategy: 'CUSTOM')]
        #[ORM\CustomIdGenerator(class: DomainIdGenerator::class)]
        public ?Id $id = null,
    ) {
    }
}
