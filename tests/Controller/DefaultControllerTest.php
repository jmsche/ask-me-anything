<?php

declare(strict_types=1);

namespace App\Tests\Controller;

final class DefaultControllerTest extends AbstractControllerTest
{
    public function testIndex(): void
    {
        static::$client->request('GET', '/');
        static::assertResponseIsSuccessful();
    }

    public function testSearch(): void
    {
        // Visible tutorial
        $crawler = static::$client->request('GET', '/search?q=opera');
        static::assertResponseIsSuccessful();
        static::assertCount(1, $crawler->filter('.card'));

        // Invisible tutorial
        $crawler = static::$client->request('GET', '/search?q=brave');
        static::assertResponseIsSuccessful();
        static::assertCount(0, $crawler->filter('.card'));

        // Invisible tutorial as admin
        $this->loginAsAdmin();
        $crawler = static::$client->request('GET', '/search?q=brave');
        static::assertResponseIsSuccessful();
        static::assertCount(0, $crawler->filter('.card'));

        // Invisible tutorial as super admin
        $this->loginAsSuperAdmin();
        $crawler = static::$client->request('GET', '/search?q=brave');
        static::assertResponseIsSuccessful();
        static::assertCount(1, $crawler->filter('.card'));
    }
}
