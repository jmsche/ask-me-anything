<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\SecurityBundle\DataCollector\SecurityDataCollector;

final class SecurityControllerTest extends AbstractControllerTest
{
    public function testLogin(): void
    {
        static::$client->request('GET', '/login');
        static::assertResponseIsSuccessful();

        // Unknown user
        $this->assertFailedLogin('user', 'user');

        // Existing user, wrong password
        $this->assertFailedLogin('admin', 'pass');

        // Login ok
        $this->submitLogin('admin', 'admin');
        static::assertResponseRedirects();
        static::assertTrue(static::getSecurityDataCollector()->isAuthenticated());

        // Already logged in: redirect
        static::$client->request('GET', '/login');
        static::assertResponseRedirects('/');
    }

    private function assertFailedLogin(string $username, string $password): void
    {
        $this->submitLogin($username, $password);
        static::assertResponseRedirects('/login');
        static::assertFalse(static::getSecurityDataCollector()->isAuthenticated());
    }

    private function submitLogin(string $username, string $password): void
    {
        $crawler = static::$client->request('GET', '/login');
        static::assertResponseIsSuccessful();
        static::$client->enableProfiler();

        $form = $crawler->selectButton('_login')->form();
        $form['username'] = $username;
        $form['password'] = $password;
        static::$client->submit($form);
    }

    private static function getSecurityDataCollector(): SecurityDataCollector
    {
        return static::$client->getProfile()->getCollector('security');
    }
}
