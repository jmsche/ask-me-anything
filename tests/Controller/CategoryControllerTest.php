<?php

declare(strict_types=1);

namespace App\Tests\Controller;

final class CategoryControllerTest extends AbstractControllerTest
{
    public function testIndex(): void
    {
        static::$client->request('GET', '/category');
        static::assertResponseRedirects('/login');

        $this->loginAsAdmin();
        static::$client->request('GET', '/category');
        static::assertResponseStatusCodeSame(403);

        $this->loginAsSuperAdmin();
        $crawler = static::$client->request('GET', '/category');
        static::assertResponseIsSuccessful();
        static::assertCount(7, $crawler->filter('tbody tr'));
    }

    public function testView(): void
    {
        $crawler = static::$client->request('GET', '/category/view/internet');
        static::assertResponseIsSuccessful();
        static::assertCount(2, $crawler->filter('.card'));

        static::$client->request('GET', '/category/view/unknown');
        static::assertResponseStatusCodeSame(404);
    }
    
    public function testCreate(): void
    {
        static::$client->request('GET', '/category/create');
        static::assertResponseRedirects('/login');

        $this->loginAsAdmin();
        static::$client->request('GET', '/category/create');
        static::assertResponseStatusCodeSame(403);

        $this->loginAsSuperAdmin();
        static::$client->request('GET', '/category/create');
        static::assertResponseIsSuccessful();
    }

    public function testUpdate(): void
    {
        static::$client->request('GET', '/category/update/7');
        static::assertResponseRedirects('/login');

        $this->loginAsAdmin();
        static::$client->request('GET', '/category/update/7');
        static::assertResponseStatusCodeSame(403);

        $this->loginAsSuperAdmin();
        static::$client->request('GET', '/category/update/100');
        static::assertResponseStatusCodeSame(404);

        static::$client->request('GET', '/category/update/7');
        static::assertResponseIsSuccessful();
    }

    public function testDelete(): void
    {
        static::$client->request('GET', '/category/delete/7');
        static::assertResponseRedirects('/login');

        $this->loginAsAdmin();
        static::$client->request('GET', '/category/delete/7');
        static::assertResponseStatusCodeSame(403);

        $this->loginAsSuperAdmin();
        static::$client->request('GET', '/category/delete/100');
        static::assertResponseStatusCodeSame(404);

        static::$client->request('GET', '/category/delete/7');
        static::assertResponseIsSuccessful();

        static::$client->request('DELETE', '/category/delete/7');
        static::assertResponseStatusCodeSame(301);

        static::$client->request('DELETE', '/category/delete/1');
        static::assertResponseStatusCodeSame(400);
    }
}
