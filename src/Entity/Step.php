<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\PrimaryKeyTrait;
use App\Entity\Traits\WeightTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Step
{
    use PrimaryKeyTrait;
    use WeightTrait;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\ManyToOne(targetEntity: Tutorial::class, inversedBy: 'steps')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Tutorial $tutorial;

    #[ORM\Column(type: Types::STRING, nullable: true)]
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

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): void
    {
        $this->file = $file;
    }

    public function getNumber(): ?int
    {
        $i = 1;
        foreach ($this->getTutorial()->getSteps() as $iStep) {
            if ($this->getId() === $iStep->getId()) {
                return $i;
                break;
            }

            ++$i;
        }

        return null;
    }
}
