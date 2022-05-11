<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\CreatedOnTrait;
use App\Entity\Traits\PrimaryKeyTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Leapt\CoreBundle\Validator\Constraints\RecaptchaV3;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class ContactMessage
{
    use CreatedOnTrait;
    use PrimaryKeyTrait;

    #[ORM\Column(type: Types::STRING, length: 100)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private ?string $author = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 10)]
    private ?string $content = null;

    #[ORM\Column(name: '`read`', type: Types::BOOLEAN)]
    private bool $read = false;

    #[RecaptchaV3]
    private $recaptcha;

    public function __construct()
    {
        $this->createdOn = new \DateTime();
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(?string $author): void
    {
        $this->author = $author;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): void
    {
        $this->content = $content;
    }

    public function isRead(): bool
    {
        return $this->read;
    }

    public function setRead(bool $read): void
    {
        $this->read = $read;
    }

    public function getRecaptcha()
    {
        return $this->recaptcha;
    }

    public function setRecaptcha($recaptcha): void
    {
        $this->recaptcha = $recaptcha;
    }
}
