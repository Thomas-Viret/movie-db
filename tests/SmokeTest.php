<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SmokeTest extends WebTestCase
{
    /**
     * @dataProvider urlProvider
     */
    public function testPageIsSuccessful($url)
    {
        $client = self::createClient();
        $client->request('GET', $url);

        $this->assertResponseIsSuccessful();
    }

    public function urlProvider()
    {
         // Home
         yield ['/'];
         // Movie show
         // @toto utiliser une base de test pour avoir des slugs 
         //du genre 'film-1' etc.
         yield ['/movie/fargo'];
         // Login
         yield ['/login'];
    }
}
