<?php

declare(strict_types=1);

namespace App\Tests\Controller;

final class LogControllerTest extends AbstractControllerTestCase
{
    public function testIndex(): void
    {
        self::$client->request('GET', '/log');
        self::assertResponseRedirects('http://localhost/login');

        $this->loginAsAdmin();
        self::$client->request('GET', '/log');
        self::assertResponseStatusCodeSame(403);

        $this->loginAsSuperAdmin();
        $crawler = self::$client->request('GET', '/log');
        self::assertResponseIsSuccessful();
        self::assertCount(500, $crawler->filter('tbody tr'));
    }

    public function testDelete(): void
    {
        self::$client->request('GET', '/log/delete');
        self::assertResponseRedirects('http://localhost/login');

        $this->loginAsAdmin();
        self::$client->request('GET', '/log/delete');
        self::assertResponseStatusCodeSame(403);

        $this->loginAsSuperAdmin();
        self::$client->request('GET', '/log/delete');
        self::assertResponseRedirects('/log');
        $crawler = self::$client->followRedirect();
        self::assertCount(0, $crawler->filter('tr'));
    }
}
