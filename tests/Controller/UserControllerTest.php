<?php

declare(strict_types=1);

namespace App\Tests\Controller;

final class UserControllerTest extends AbstractControllerTest
{
    public function testIndex(): void
    {
        self::$client->request('GET', '/user');
        self::assertResponseRedirects('http://localhost/login');

        $this->loginAsAdmin();
        self::$client->request('GET', '/user');
        self::assertResponseStatusCodeSame(403);

        $this->loginAsSuperAdmin();
        $crawler = self::$client->request('GET', '/user');
        self::assertResponseIsSuccessful();
        self::assertCount(3, $crawler->filter('tbody tr'));
    }

    public function testCreate(): void
    {
        self::$client->request('GET', '/user/create');
        self::assertResponseRedirects('http://localhost/login');

        $this->loginAsAdmin();
        self::$client->request('GET', '/user/create');
        self::assertResponseStatusCodeSame(403);

        $this->loginAsSuperAdmin();
        self::$client->request('GET', '/user/create');
        self::assertResponseIsSuccessful();
    }

    public function testUpdate(): void
    {
        self::$client->request('GET', '/user/update/3');
        self::assertResponseRedirects('http://localhost/login');

        $this->loginAsAdmin();
        self::$client->request('GET', '/user/update/3');
        self::assertResponseStatusCodeSame(403);

        $this->loginAsSuperAdmin();
        self::$client->request('GET', '/user/update/100');
        self::assertResponseStatusCodeSame(404);

        self::$client->request('GET', '/user/update/3');
        self::assertResponseIsSuccessful();
    }

    public function testDelete(): void
    {
        self::$client->request('GET', '/user/delete/3');
        self::assertResponseRedirects('http://localhost/login');

        $this->loginAsAdmin();
        self::$client->request('GET', '/user/delete/3');
        self::assertResponseStatusCodeSame(403);

        $this->loginAsSuperAdmin();
        self::$client->request('GET', '/user/delete/100');
        self::assertResponseStatusCodeSame(404);

        self::$client->request('GET', '/user/delete/3');
        self::assertResponseIsSuccessful();

        self::$client->request('DELETE', '/user/delete/3');
        self::assertResponseStatusCodeSame(301);
    }
}
