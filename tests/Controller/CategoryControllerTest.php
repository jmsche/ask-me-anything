<?php

declare(strict_types=1);

namespace App\Tests\Controller;

final class CategoryControllerTest extends AbstractControllerTest
{
    public function testIndex(): void
    {
        self::$client->request('GET', '/category');
        self::assertResponseRedirects('http://localhost/login');

        $this->loginAsAdmin();
        self::$client->request('GET', '/category');
        self::assertResponseStatusCodeSame(403);

        $this->loginAsSuperAdmin();
        $crawler = self::$client->request('GET', '/category');
        self::assertResponseIsSuccessful();
        self::assertCount(7, $crawler->filter('tbody tr'));
    }

    public function testView(): void
    {
        $crawler = self::$client->request('GET', '/category/view/internet');
        self::assertResponseIsSuccessful();
        self::assertCount(2, $crawler->filter('.card'));

        self::$client->request('GET', '/category/view/unknown');
        self::assertResponseStatusCodeSame(404);
    }

    public function testCreate(): void
    {
        self::$client->request('GET', '/category/create');
        self::assertResponseRedirects('http://localhost/login');

        $this->loginAsAdmin();
        self::$client->request('GET', '/category/create');
        self::assertResponseStatusCodeSame(403);

        $this->loginAsSuperAdmin();
        self::$client->request('GET', '/category/create');
        self::assertResponseIsSuccessful();
    }

    public function testUpdate(): void
    {
        self::$client->request('GET', '/category/update/7');
        self::assertResponseRedirects('http://localhost/login');

        $this->loginAsAdmin();
        self::$client->request('GET', '/category/update/7');
        self::assertResponseStatusCodeSame(403);

        $this->loginAsSuperAdmin();
        self::$client->request('GET', '/category/update/100');
        self::assertResponseStatusCodeSame(404);

        self::$client->request('GET', '/category/update/7');
        self::assertResponseIsSuccessful();
    }

    public function testDelete(): void
    {
        self::$client->request('GET', '/category/delete/7');
        self::assertResponseRedirects('http://localhost/login');

        $this->loginAsAdmin();
        self::$client->request('GET', '/category/delete/7');
        self::assertResponseStatusCodeSame(403);

        $this->loginAsSuperAdmin();
        self::$client->request('GET', '/category/delete/100');
        self::assertResponseStatusCodeSame(404);

        self::$client->request('GET', '/category/delete/7');
        self::assertResponseIsSuccessful();

        self::$client->request('DELETE', '/category/delete/7');
        self::assertResponseStatusCodeSame(301);

        self::$client->request('DELETE', '/category/delete/1');
        self::assertResponseStatusCodeSame(400);
    }
}
