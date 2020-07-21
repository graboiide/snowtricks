<?php


namespace App\Tests\Controller;


use App\Controller\HomeController;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class HomeControllerTest extends WebTestCase
{
    /**
     * Vérifie que la page est chargé
     */
    public function testHomePage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET','/');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testCardsLoadedHomePage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET','/');
        $this->assertSelectorExists('.card');

    }



}