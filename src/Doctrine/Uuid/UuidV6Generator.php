<?php
declare(strict_types=1);

namespace App\Doctrine\Uuid;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Id\AbstractIdGenerator;
use Symfony\Component\Uid\Factory\UuidFactory;

class UuidV6Generator extends AbstractIdGenerator
{
    private UuidFactory $factory;

    public function __construct(UuidFactory $factory)
    {
        $this->factory = $factory;
    }

    public function generate(EntityManager $em, $entity)
    {
        return $this->factory->timeBased()->create();
    }
}
