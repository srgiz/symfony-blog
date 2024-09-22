<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine;

use App\Domain\Blog\Entity\Id;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AbstractIdGenerator;

class DomainIdGenerator extends AbstractIdGenerator
{
    #[\Override]
    public function generateId(EntityManagerInterface $em, ?object $entity): Id
    {
        return new Id();
    }

    #[\Override]
    public function isPostInsertGenerator(): bool
    {
        return false;
    }
}
