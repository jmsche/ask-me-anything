<?php

declare(strict_types=1);

namespace App\Tests\Controller;

final class ContactControllerTest extends AbstractControllerTestCase
{
    public function testIndex(): void
    {
        self::$client->request('GET', '/contact');
        self::assertResponseRedirects('http://localhost/login');

        $this->loginAsAdmin();
        self::$client->request('GET', '/contact');
        self::assertResponseStatusCodeSame(403);

        $this->loginAsSuperAdmin();
        $crawler = self::$client->request('GET', '/contact');
        self::assertResponseIsSuccessful();
        self::assertCount(10, $crawler->filter('tbody tr'));
    }

    public function testView(): void
    {
        self::$client->request('GET', '/contact/view/1');
        self::assertResponseRedirects('http://localhost/login');

        $this->loginAsAdmin();
        self::$client->request('GET', '/contact/view/1');
        self::assertResponseStatusCodeSame(403);

        $this->loginAsSuperAdmin();
        $crawler = self::$client->request('GET', '/contact/view/1');
        self::assertResponseIsSuccessful();

        self::$client->request('GET', '/contact/view/100');
        self::assertResponseStatusCodeSame(404);
    }

    public function testCreate(): void
    {
        self::$client->request('GET', '/contact/create');
        self::assertResponseIsSuccessful();

        $this->loginAsAdmin();
        self::$client->request('GET', '/contact/create');
        self::assertResponseIsSuccessful();

        $this->loginAsSuperAdmin();
        self::$client->request('GET', '/contact/create');
        self::assertResponseIsSuccessful();
    }

    public function testDelete(): void
    {
        self::$client->request('GET', '/contact/delete/1');
        self::assertResponseRedirects('http://localhost/login');

        $this->loginAsAdmin();
        self::$client->request('GET', '/contact/delete/1');
        self::assertResponseStatusCodeSame(403);

        $this->loginAsSuperAdmin();
        self::$client->request('GET', '/contact/delete/100');
        self::assertResponseStatusCodeSame(404);

        self::$client->request('GET', '/contact/delete/1');
        self::assertResponseIsSuccessful();

        self::$client->request('DELETE', '/contact/delete/1');
        self::assertResponseStatusCodeSame(301);
    }
}
