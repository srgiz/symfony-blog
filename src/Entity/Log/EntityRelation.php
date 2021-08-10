<?php
declare(strict_types=1);

namespace App\Entity\Log;

use App\Repository\Log\EntityRelationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EntityRelationRepository::class)]
#[ORM\Table(name: 'log_entity_relation')]
class EntityRelation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private ?int $id;

    #[ORM\Column(type: 'uuid')]
    private ?Uuid $uuid;

    #[ORM\Column(type: 'string')]
    private ?string $related;

    #[ORM\ManyToOne(targetEntity: Entity::class, cascade: ['all'], inversedBy: 'relations')]
    #[ORM\JoinColumn(name: 'uuid', referencedColumnName: 'uuid', onDelete: 'CASCADE')]
    private ?Entity $entity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function setUuid(?Uuid $uuid): void
    {
        $this->uuid = $uuid;
    }

    public function getRelated(): ?string
    {
        return $this->related;
    }

    public function setRelated(?string $related): void
    {
        $this->related = $related;
    }

    public function getEntity(): ?Entity
    {
        return $this->entity;
    }

    public function setEntity(?Entity $entity): void
    {
        $this->entity = $entity;
    }
}
