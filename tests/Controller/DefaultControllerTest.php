<?php

declare(strict_types=1);

namespace App\Tests\Controller;

final class DefaultControllerTest extends AbstractControllerTest
{
    public function testIndex(): void
    {
        self::$client->request('GET', '/');
        self::assertResponseIsSuccessful();
    }

    public function testSearch(): void
    {
        // Visible tutorial
        $crawler = self::$client->request('GET', '/search?q=opera');
        self::assertResponseIsSuccessful();
        self::assertCount(1, $crawler->filter('.card'));

        // Invisible tutorial
        $crawler = self::$client->request('GET', '/search?q=brave');
        self::assertResponseIsSuccessful();
        self::assertCount(0, $crawler->filter('.card'));

        // Invisible tutorial as admin
        $this->loginAsAdmin();
        $crawler = self::$client->request('GET', '/search?q=brave');
        self::assertResponseIsSuccessful();
        self::assertCount(0, $crawler->filter('.card'));

        // Invisible tutorial as super admin
        $this->loginAsSuperAdmin();
        $crawler = self::$client->request('GET', '/search?q=brave');
        self::assertResponseIsSuccessful();
        self::assertCount(1, $crawler->filter('.card'));
    }
}
