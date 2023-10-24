<?php

namespace App\Core\Security\Repository;

use App\Core\Security\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * @template T of User
 * @template-extends EntityRepository<T>
 */
class UserRepository extends EntityRepository
{
}
