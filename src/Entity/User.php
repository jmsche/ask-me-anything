<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\PrimaryKeyTrait;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    use PrimaryKeyTrait;

    private const ROLE_ADMIN = 'ROLE_ADMIN';
    private const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    /**
     * @ORM\Column(type="string", length=30, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(max=30)
     */
    private ?string $username = null;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Choice(callback="getAvailableRoles")
     */
    private ?string $role = null;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $password = null;

    /**
     * @Assert\NotBlank(groups={"Create"})
     */
    private ?string $plainPassword = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getRoles(): array
    {
        return [$this->role];
    }

    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): void
    {
        $this->role = $role;
    }

    public static function getAvailableRoles(): array
    {
        return [
            self::ROLE_ADMIN,
            self::ROLE_SUPER_ADMIN,
        ];
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }
}
