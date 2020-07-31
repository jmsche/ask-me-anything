<?php

declare(strict_types=1);

namespace App\Faker\Provider;

use Faker\Generator;
use Faker\Provider\Base as BaseProvider;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserProvider extends BaseProvider
{
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(Generator $generator, UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct($generator);
        $this->passwordEncoder = $passwordEncoder;
    }

    public function encodePassword(UserInterface $user, string $password): ?string
    {
        return $this->passwordEncoder->encodePassword($user, $password);
    }
}
