<?php
declare(strict_types=1);

namespace App\Repository\User;

use App\Entity\User\User;
use App\Entity\User\UserToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @method UserToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserToken[]    findAll()
 * @method UserToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserTokenRepository extends ServiceEntityRepository
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(ManagerRegistry $registry, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct($registry, UserToken::class);
        $this->passwordHasher = $passwordHasher;
    }

    public function findByKey(string $token): ?UserToken
    {
        return $this->findOneBy([
            'token' => $token,
        ]);
    }

    public function createNew(User $user): UserToken
    {
        $token = $this->passwordHasher->hashPassword($user, $user->getPassword());

        $userToken = (new UserToken())
            ->setToken($token)
            ->setUser($user)
        ;

        $this->_em->persist($userToken);
        $this->_em->flush();

        return $userToken;
    }
}
