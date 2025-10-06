<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Column(name: 'id', type: 'integer', nullable: false, options: ['unsigned' => true])]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true, nullable: false)]
    #[Assert\NotBlank(groups: ['r,egister:create'])]
    #[Assert\Email(groups: ['register:create'])]
    #[Groups(['register:create', 'user:update', 'user:read'])]
    private ?string $email = null;

    #[ORM\Column(name: 'roles', type: 'json', nullable: false)]
    #[Groups(['register:create', 'user:update', 'user:read'])]
    private array $roles = [];

    #[ORM\Column(name: 'password', type: 'string', length: 255, nullable: false)]
    #[Assert\NotBlank]
    #[Groups(['register:create'])]
    private ?string $password = null;

    #[Groups(['register:create'])]
    private ?string $passwordPlain = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function eraseCredentials(): void
    {
        $this->passwordPlain = null;
    }


    public function getPasswordPlain(): string
    {
        return $this->passwordPlain;
    }

    public function setPasswordPlain(string $password): User
    {
        $this->passwordPlain = $password;
        return $this;
    }
}
