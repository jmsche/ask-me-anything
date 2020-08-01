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
        static::$client->request('GET', '/search?q=test');
        static::assertResponseIsSuccessful();
    }
}
