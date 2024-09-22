<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Entity;

use App\Domain\Blog\Entity\Id;
use App\Infrastructure\Doctrine\Repository\UserDataRepository;
use App\Infrastructure\Doctrine\Types\IdType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserDataRepository::class)]
#[ORM\Table('users')]
#[UniqueEntity('email')]
class UserData implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: IdType::NAME)]
        private readonly Id $id,
        #[ORM\Column(type: 'string', length: 255, unique: true)]
        #[Assert\NotBlank]
        #[Assert\Email]
        private readonly string $email,
        #[ORM\Column(name: 'password_hash', type: 'string', length: 255)]
        #[Assert\NotBlank]
        private ?string $passwordHash = null,
        #[ORM\OneToMany(targetEntity: UserTokenData::class, mappedBy: 'user')]
        private Collection $tokens = new ArrayCollection(),
    ) {
        $this->tokens = new ArrayCollection();
    }

    public function getId(): Id
    {
        return $this->id;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->passwordHash;
    }

    public function setPassword(#[\SensitiveParameter] ?string $hash): self
    {
        $this->passwordHash = $hash;

        return $this;
    }

    public function getRoles(): array
    {
        return ['ROLE_ADMIN'];
    }

    public function eraseCredentials(): void
    {
    }
}
