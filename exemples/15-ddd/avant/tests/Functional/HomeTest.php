<?php

namespace App\Tests\Functional;

class HomeTest extends FunctionalTestCase
{
    public function testPageAccueilChargee(): void
    {
        $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('nav');
        $this->assertSelectorTextContains('h1', 'Domain Driven Design');
    }

    public function testLiensNavigationPresents(): void
    {
        $this->client->request('GET', '/');

        $this->assertSelectorExists('a[href="/commandes/passer"]');
        $this->assertSelectorExists('a[href="/commandes"]');
    }
}
