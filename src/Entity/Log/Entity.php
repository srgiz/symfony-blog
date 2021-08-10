<?php
declare(strict_types=1);

namespace App\Entity\Log;

use App\Doctrine\Uuid\UuidV6Generator;
use App\Repository\Log\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: EntityRepository::class)]
#[ORM\Table(name: 'log_entity')]
class Entity
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidV6Generator::class)]
    private ?Uuid $uuid;

    #[ORM\Column(type: 'string', length: 16)]
    private ?string $name;

    #[ORM\Column(type: 'json')]
    private array $changeSet = [];

    #[ORM\Column(type: 'datetime_immutable_ms', precision: 6, options: ['default' => 'CURRENT_TIMESTAMP(6)'])]
    private ?\DateTimeImmutable $created_at;

    #[ORM\OneToMany(mappedBy: 'entity', targetEntity: EntityRelation::class)]
    private Collection $relations;

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
        $this->relations = new ArrayCollection();
    }

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getChangeSet(): array
    {
        return $this->changeSet;
    }

    public function setChangeSet(array $changeSet): void
    {
        $this->changeSet = $changeSet;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getRelations(): Collection
    {
        return $this->relations;
    }

    public function addRelation(EntityRelation $relation): void
    {
        if ($this->relations->contains($relation))
            return;

        $relation->setEntity($this);
        $this->relations->add($relation);
    }
}
