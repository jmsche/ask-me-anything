<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\CreatedOnTrait;
use App\Entity\Traits\PrimaryKeyTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class Tutorial
{
    use PrimaryKeyTrait;
    use CreatedOnTrait;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description = null;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class)
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     */
    private ?Category $category = null;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $locked = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $visible = true;

    /**
     * @ORM\OneToMany(targetEntity=Step::class, mappedBy="tutorial")
     * @ORM\OrderBy({ "weight" = "ASC" })
     */
    private Collection $steps;

    public function __construct()
    {
        $this->createdOn = new \DateTime();
        $this->steps = new ArrayCollection();
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): void
    {
        $this->category = $category;
    }

    public function isLocked(): bool
    {
        return $this->locked;
    }

    public function setLocked(bool $locked): void
    {
        $this->locked = $locked;
    }

    public function isVisible(): bool
    {
        return $this->visible;
    }

    public function setVisible(bool $visible): void
    {
        $this->visible = $visible;
    }

    public function getSteps(): Collection
    {
        return $this->steps;
    }

    public function getFirstStep(): ?Step
    {
        if (0 < $this->steps->count()) {
            return $this->steps->first();
        }

        return null;
    }

    public function getLastStep(): ?Step
    {
        if (0 < $this->steps->count()) {
            return $this->steps->last();
        }

        return null;
    }
}
