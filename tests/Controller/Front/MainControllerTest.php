<?php


namespace App\Tests\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Etendre de WebTestCase nous permet de faire des tests fonctionnels
 * sur l'appli Symfo
 */

class MainControllerTest extends WebTestCase
{
    public function testhome()
    {
        $client = static::createClient();

        $client->request('GET', '/');


        // Contenu du h1
        $this->assertSelectorTextContains('h1', 'Tous les films');

        // Status 200
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testMovieShow()
    {
        $client = static::createClient();

        $client->request('GET', '/movie/fargo');

        

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }


    /**
     * L'anonyme n'as pas accès à l'ajout d'une critique
     * En GET
     */
    public function testReviewAddFailure()
    {
        // Crée un client
        $client = static::createClient();
        // Exécute une requête en GET sur la route '/review/add'
        $crawler = $client->request('GET', '/review/add');

        // La réponse a un statut 3xx car redirection vers /login
        $this->assertResponseRedirects();
        // $this->assertResponseStatusCodeSame(302);
    }

    /**
     * L'anonyme n'as pas accès à l'ajout d'une critique
     * En POST
     */
    public function testReviewAddFailurePost()
    {
        // Crée un client
        $client = static::createClient();
        // Exécute une requête en GET sur la route '/review/add'
        $crawler = $client->request('POST', '/review/add');

        // La réponse a un statut 3xx car redirection vers /login
        $this->assertResponseRedirects();
        // $this->assertResponseStatusCodeSame(302);
    }
}