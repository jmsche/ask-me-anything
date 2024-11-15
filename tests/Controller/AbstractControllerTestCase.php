<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractControllerTestCase extends WebTestCase
{
    protected static KernelBrowser $client;

    protected function setUp(): void
    {
        self::$client = self::createClient();
    }

    protected function loginAsSuperAdmin(): void
    {
        $this->login(2);
    }

    protected function loginAsAdmin(): void
    {
        $this->login(1);
    }

    private function login(int $userId): void
    {
        $user = self::$client->getContainer()->get('doctrine.orm.entity_manager')
            ->getRepository(User::class)->find($userId);
        self::$client->loginUser($user);
    }
}
