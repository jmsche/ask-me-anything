<?php

declare(strict_types=1);

namespace App\Tests\Controller;

final class UserControllerTest extends AbstractControllerTest
{
    public function testIndex(): void
    {
        static::$client->request('GET', '/user');
        static::assertResponseRedirects('/login');

        $this->loginAsAdmin();
        static::$client->request('GET', '/user');
        static::assertResponseStatusCodeSame(403);

        $this->loginAsSuperAdmin();
        $crawler = static::$client->request('GET', '/user');
        static::assertResponseIsSuccessful();
        static::assertCount(3, $crawler->filter('tbody tr'));
    }
    
    public function testCreate(): void
    {
        static::$client->request('GET', '/user/create');
        static::assertResponseRedirects('/login');

        $this->loginAsAdmin();
        static::$client->request('GET', '/user/create');
        static::assertResponseStatusCodeSame(403);

        $this->loginAsSuperAdmin();
        static::$client->request('GET', '/user/create');
        static::assertResponseIsSuccessful();
    }

    public function testUpdate(): void
    {
        static::$client->request('GET', '/user/update/3');
        static::assertResponseRedirects('/login');

        $this->loginAsAdmin();
        static::$client->request('GET', '/user/update/3');
        static::assertResponseStatusCodeSame(403);

        $this->loginAsSuperAdmin();
        static::$client->request('GET', '/user/update/100');
        static::assertResponseStatusCodeSame(404);

        static::$client->request('GET', '/user/update/3');
        static::assertResponseIsSuccessful();
    }

    public function testDelete(): void
    {
        static::$client->request('GET', '/user/delete/3');
        static::assertResponseRedirects('/login');

        $this->loginAsAdmin();
        static::$client->request('GET', '/user/delete/3');
        static::assertResponseStatusCodeSame(403);

        $this->loginAsSuperAdmin();
        static::$client->request('GET', '/user/delete/100');
        static::assertResponseStatusCodeSame(404);

        static::$client->request('GET', '/user/delete/3');
        static::assertResponseIsSuccessful();

        static::$client->request('DELETE', '/user/delete/3');
        static::assertResponseStatusCodeSame(301);
    }
}
