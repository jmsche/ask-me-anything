<?php

declare(strict_types=1);

namespace App\Tests\Controller;

final class LogControllerTest extends AbstractControllerTest
{
    public function testIndex(): void
    {
        static::$client->request('GET', '/log');
        static::assertResponseRedirects('/login');

        $this->loginAsAdmin();
        static::$client->request('GET', '/log');
        static::assertResponseStatusCodeSame(403);

        $this->loginAsSuperAdmin();
        $crawler = static::$client->request('GET', '/log');
        static::assertResponseIsSuccessful();
        static::assertCount(500, $crawler->filter('tbody tr'));
    }

    public function testDelete(): void
    {
        static::$client->request('GET', '/log/delete');
        static::assertResponseRedirects('/login');

        $this->loginAsAdmin();
        static::$client->request('GET', '/log/delete');
        static::assertResponseStatusCodeSame(403);

        $this->loginAsSuperAdmin();
        static::$client->request('GET', '/log/delete');
        static::assertResponseRedirects('/log');
        $crawler = static::$client->followRedirect();
        static::assertCount(0, $crawler->filter('tr'));
    }
}
