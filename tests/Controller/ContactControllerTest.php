<?php

declare(strict_types=1);

namespace App\Tests\Controller;

final class ContactControllerTest extends AbstractControllerTest
{
    public function testIndex(): void
    {
        static::$client->request('GET', '/contact');
        static::assertResponseRedirects('/login');

        $this->loginAsAdmin();
        static::$client->request('GET', '/contact');
        static::assertResponseStatusCodeSame(403);

        $this->loginAsSuperAdmin();
        $crawler = static::$client->request('GET', '/contact');
        static::assertResponseIsSuccessful();
        static::assertCount(10, $crawler->filter('tbody tr'));
    }

    public function testView(): void
    {
        static::$client->request('GET', '/contact/view/1');
        static::assertResponseRedirects('/login');

        $this->loginAsAdmin();
        static::$client->request('GET', '/contact/view/1');
        static::assertResponseStatusCodeSame(403);

        $this->loginAsSuperAdmin();
        $crawler = static::$client->request('GET', '/contact/view/1');
        static::assertResponseIsSuccessful();

        static::$client->request('GET', '/contact/view/100');
        static::assertResponseStatusCodeSame(404);
    }

    public function testCreate(): void
    {
        static::$client->request('GET', '/contact/create');
        static::assertResponseIsSuccessful();

        $this->loginAsAdmin();
        static::$client->request('GET', '/contact/create');
        static::assertResponseIsSuccessful();

        $this->loginAsSuperAdmin();
        static::$client->request('GET', '/contact/create');
        static::assertResponseIsSuccessful();
    }

    public function testDelete(): void
    {
        static::$client->request('GET', '/contact/delete/1');
        static::assertResponseRedirects('/login');

        $this->loginAsAdmin();
        static::$client->request('GET', '/contact/delete/1');
        static::assertResponseStatusCodeSame(403);

        $this->loginAsSuperAdmin();
        static::$client->request('GET', '/contact/delete/100');
        static::assertResponseStatusCodeSame(404);

        static::$client->request('GET', '/contact/delete/1');
        static::assertResponseIsSuccessful();

        static::$client->request('DELETE', '/contact/delete/1');
        static::assertResponseStatusCodeSame(301);
    }
}
