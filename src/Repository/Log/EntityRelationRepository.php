<?php
declare(strict_types=1);

namespace App\Repository\Log;

use App\Entity\Log\EntityRelation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EntityRelation|null find($id, $lockMode = null, $lockVersion = null)
 * @method EntityRelation|null findOneBy(array $criteria, array $orderBy = null)
 * @method EntityRelation[]    findAll()
 * @method EntityRelation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EntityRelationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EntityRelation::class);
    }
}
