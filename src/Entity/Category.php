<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\PrimaryKeyTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @UniqueEntity(fields={"slug"})
 * @UniqueEntity(fields={"icon"})
 */
class Category
{
    use PrimaryKeyTrait;

    /**
     * @ORM\Column(type="string", length=40)
     * @Assert\NotBlank()
     * @Assert\Length(max=40)
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="string", length=40, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(max=40)
     * @Assert\Regex(pattern="/^([a-z0-9-]+)$/", message="regex.slug_format")
     */
    private ?string $slug = null;

    /**
     * @ORM\Column(type="string", length=20, unique=true)
     * @Assert\NotBlank()
     * @Assert\Length(max=20)
     */
    private ?string $icon = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(?string $icon): void
    {
        $this->icon = $icon;
    }
}
