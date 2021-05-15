<?php

namespace App\Tests\Controller\Back;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AnonymousTest extends WebTestCase
{
    /**
     * Annotation suivi du nom de la méthode qui fourni les données
     *
     * @dataProvider urlProvider
     */
    public function testRedirectInGet($url): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', $url);

        // On est bien redirigé vers le login
        $this->assertResponseStatusCodeSame(302);
    }
    public function urlProvider()
    {
        yield ['/back/user/browse'];
        yield ['/back/user/add'];
        yield ['/back/user/read/11'];
        yield ['/back/user/edit/11'];
        yield ['/back/job/browse'];
        yield ['/back/job/read/11'];
        yield ['/back/movie/browse'];
        yield ['/back/movie/add'];
        yield ['/back/movie/read/11'];
        yield ['/back/movie/edit/11'];
    }


    /**
     * @dataProvider urlProviderForPost
     */
    public function testRedirectInPost($url)
    {
        $client = self::createClient();
        $client->request('POST', $url);

        $this->assertResponseStatusCodeSame(302);
        // $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function urlProviderForPost()
    {
        yield ['/back/user/add'];
        yield ['/back/user/edit/11'];
        yield ['/back/movie/add'];
        yield ['/back/movie/edit/11'];
    }

    /**
     * @dataProvider urlProviderForDelete
     */
    public function testRedirectInDelete($url)
    {
        $client = self::createClient();
        $client->request('DELETE', $url);

        $this->assertResponseStatusCodeSame(302);
        // $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }

    public function urlProviderForDelete()
    {
        yield ['/back/user/delete/2'];
        yield ['/back/movie/delete/2'];
    }
}
