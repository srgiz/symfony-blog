<?php
declare(strict_types=1);

namespace App\Security\Profile;

use App\Core\Dto\Response\JsonResponseDto;
use App\Core\Http\HeadersHandlerInterface;
use App\Exception\HttpException;
use App\Security\Entity\User;
use App\Security\Entity\UserToken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

readonly class CurrentProfile
{
    public function __construct(
        private EntityManagerInterface $em,
        private TokenStorageInterface $currentToken,
        private UserPasswordHasherInterface $passwordHasher,
        private HeadersHandlerInterface $headers,
        #[Autowire('%app.security.token.cookie%')] private string $name,
        #[Autowire('%app.security.token.expire%')] private string $expire,
    ) {}

    public function getCookieName(): string
    {
        return $this->name;
    }

    public function createCookie(UserToken $token): Cookie
    {
        return Cookie::create($this->getCookieName(), $token->getToken(), strtotime($this->expire));
    }

    public function register(string $email, string $password): JsonResponseDto
    {
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($this->passwordHasher->hashPassword($user, $password));

        $userToken = $this->createUserToken($user);

        $this->em->persist($userToken);
        $this->em->flush();

        $this->headers->setCookie($this->createCookie($userToken));

        return new JsonResponseDto(null);
    }

    private function createUserToken(User $user): UserToken
    {
        $token = $this->passwordHasher->hashPassword($user, $user->getPassword());

        return (new UserToken())
            ->setToken($token)
            ->setUser($user)
        ;
    }

    public function upgradePassword(string $oldPassword, string $newPassword): JsonResponseDto
    {
        $user = $this->currentToken?->getToken()?->getUser();

        if (!$user instanceof User) {
            throw new HttpException(403);
        }

        if (!$this->passwordHasher->isPasswordValid($user, $oldPassword)) {
            throw new HttpException(403, 'Wrong password');
        }

        $user->setPassword($this->passwordHasher->hashPassword($user, $newPassword));
        $userToken = $this->createUserToken($user);

        $this->em->persist($userToken);
        $this->em->flush();

        $this->headers->setCookie($this->createCookie($userToken));

        return new JsonResponseDto(null);
    }
}
