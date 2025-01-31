<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Blog\Entity\Id;
use App\Domain\Blog\Entity\User;
use App\Domain\Blog\Repository\UserRepositoryInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

readonly class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        private Connection $connection,
        private PasswordHasherFactoryInterface $hasherFactory,
    ) {
    }

    public function findByToken(string $token): ?User
    {
        $queryBuilder = $this->connection->createQueryBuilder()->from('users', 'u')
            ->select('u.*')
            ->innerJoin('u', 'user_token', 't', 't.user_id = u.id AND t.token = :token')
            ->setParameter('token', $token);

        $data = $queryBuilder->executeQuery()->fetchAssociative();

        return $data ? $this->transformData($data) : null;
    }

    public function findByEmail(string $email): ?User
    {
        $queryBuilder = $this->connection->createQueryBuilder()->from('users', 'u')
            ->select('u.*')
            ->where('u.email = :email')
            ->setParameter('email', $email);

        $data = $queryBuilder->executeQuery()->fetchAssociative();

        return $data ? $this->transformData($data) : null;
    }

    private function transformData(array $data): User
    {
        return new User(
            id: new Id($data['id']),
            email: $data['email'],
            password: $data['password_hash'],
        );
    }

    public function create(Id $id, string $email, #[\SensitiveParameter] string $plainPassword): void
    {
        $hasher = $this->hasherFactory->getPasswordHasher(User::class);
        $passwordHash = $hasher->hash($plainPassword);

        $sql = 'INSERT INTO users (id, email, password_hash) VALUES (:id, :email, :password_hash)';

        $this->connection->executeStatement($sql, [
            'id' => $id,
            'email' => $email,
            'password_hash' => $passwordHash,
        ]);
    }

    public function addToken(User $user, string $token): void
    {
        //$hasher = $this->hasherFactory->getPasswordHasher(User::class);
        // токен является хешем на хеш пароля
        //$tokenHash = $hasher->hash((string) $user->getPassword());

        $sql = 'INSERT INTO user_token (id, user_id, token) VALUES (:id, :user_id, :token)';

        $this->connection->executeStatement($sql, [
            'id' => new Id(),
            'user_id' => $user->getId(),
            'token' => $token,
        ]);
    }
}
