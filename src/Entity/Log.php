<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\CreatedOnTrait;
use App\Entity\Traits\PrimaryKeyTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Log
{
    use CreatedOnTrait;
    use PrimaryKeyTrait;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $ipAddress = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $userAgent = null;

    /**
     * @ORM\Column(type="string")
     */
    private string $url;

    /**
     * @ORM\Column(type="string")
     */
    private string $label;

    public function __construct(?string $ipAddress, ?string $userAgent, string $url, string $label)
    {
        $this->createdOn = new \DateTime();
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
        $this->url = $url;
        $this->label = $label;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getLabel(): string
    {
        return $this->label;
    }
}
