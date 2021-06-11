<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Serializable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="system_user")
 * @UniqueEntity(fields="identifier", message="This identifier is already in use")
 */
class User implements UserInterface, Serializable
{
    public const ROLES
        = [
            'User'  => 'ROLE_USER',
            'Admin' => 'ROLE_ADMIN',
        ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = 0;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank
     */
    private string $identifier = '';

    /**
     * @ORM\Column(type="string", length=50)
     */
    private string $role = 'ROLE_USER';

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private ?string $googleId = '';

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $gitHubId = '';

    public function eraseCredentials(): void
    {
    }

    public function getRoles(): array
    {
        return [$this->getRole()];
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserIdentifier(): string
    {
        return $this->identifier;
    }

    public function setUserIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->identifier;
    }

    public function getPassword(): ?string
    {
        return null;
    }

    public function getSalt(): void
    {
    }

    public function serialize(): string
    {
        return serialize(
            [
                $this->id,
                $this->identifier,
            ]
        );
    }

    public function unserialize($serialized): void
    {
        [
            $this->id,
            $this->identifier,
        ]
            = unserialize($serialized, ['allowed_classes' => [__CLASS__]]);
    }

    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    public function setGoogleId(?string $googleId): self
    {
        $this->googleId = $googleId;

        return $this;
    }

    public function getGitHubId(): ?string
    {
        return $this->gitHubId;
    }

    public function setGitHubId(?string $gitHubId): self
    {
        $this->gitHubId = $gitHubId;

        return $this;
    }
}
