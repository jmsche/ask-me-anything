<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

trait CreatedOnTrait
{
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTime $createdOn;

    public function getCreatedOn(): \DateTime
    {
        return $this->createdOn;
    }
}
