<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\PrimaryKeyTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class Step
{
    use PrimaryKeyTrait;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $content = null;

    /**
     * @ORM\ManyToOne(targetEntity=Tutorial::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private Tutorial $tutorial;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     */
    private ?int $weight = 1;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $file = null;

    public function __construct(Tutorial $tutorial)
    {
        $this->tutorial = $tutorial;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    public function getTutorial(): Tutorial
    {
        return $this->tutorial;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(?int $weight): void
    {
        $this->weight = $weight;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): void
    {
        $this->file = $file;
    }
}
