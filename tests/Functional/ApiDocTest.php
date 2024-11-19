<?php

declare(strict_types=1);

namespace App\Tests\Functional;

class ApiDocTest extends FunctionalTestCase
{
    public function testSuccess(): void
    {
        self::assertEquals(200, $this->request('GET', '/doc')->getStatusCode());
        self::assertEquals(200, $this->request('GET', '/doc.json')->getStatusCode());
    }
}
