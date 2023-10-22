<?php

namespace App\Domain\Security\Repository;

use App\Domain\Security\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * @template T of User
 * @template-extends EntityRepository<T>
 */
class UserRepository extends EntityRepository
{
}
