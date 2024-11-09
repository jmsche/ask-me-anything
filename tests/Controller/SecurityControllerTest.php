<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\SecurityBundle\DataCollector\SecurityDataCollector;

final class SecurityControllerTest extends AbstractControllerTestCase
{
    public function testLogin(): void
    {
        self::$client->request('GET', '/login');
        self::assertResponseIsSuccessful();

        // Unknown user
        $this->assertFailedLogin('user', 'user');

        // Existing user, wrong password
        $this->assertFailedLogin('admin', 'pass');

        // Login ok
        $this->submitLogin('admin', 'admin');
        self::assertResponseRedirects();
        self::assertTrue(self::getSecurityDataCollector()->isAuthenticated());

        // Already logged in: redirect
        self::$client->request('GET', '/login');
        self::assertResponseRedirects('/');
    }

    private function assertFailedLogin(string $username, string $password): void
    {
        $this->submitLogin($username, $password);
        self::assertResponseRedirects('http://localhost/login');
        self::assertFalse(self::getSecurityDataCollector()->isAuthenticated());
    }

    private function submitLogin(string $username, string $password): void
    {
        $crawler = self::$client->request('GET', '/login');
        self::assertResponseIsSuccessful();
        self::$client->enableProfiler();

        $form = $crawler->selectButton('_login')->form();
        $form['username'] = $username;
        $form['password'] = $password;
        self::$client->submit($form);
    }

    private static function getSecurityDataCollector(): SecurityDataCollector
    {
        return self::$client->getProfile()->getCollector('security');
    }
}
