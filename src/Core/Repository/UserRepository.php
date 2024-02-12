<?php

declare(strict_types=1);

namespace App\Core\Repository;

use App\Core\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * @template T of User
 * @template-extends EntityRepository<T>
 */
class UserRepository extends EntityRepository
{
}
