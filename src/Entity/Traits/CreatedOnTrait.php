<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait CreatedOnTrait
{
    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdOn;

    public function getCreatedOn(): \DateTime
    {
        return $this->createdOn;
    }
}
