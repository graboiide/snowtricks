<?php


namespace App\Tests\Controller;


use App\Controller\HomeController;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AccountControllerTest extends WebTestCase
{
    /**
     * Vérifie que la page est chargé
     */
    public function testLoginDisplay()
    {
        $client = static::createClient();
        $crawler = $client->request('GET','/login');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1','Se connecter !');
        $this->assertSelectorNotExists('.alert-danger');
    }

    public function testErrorLogin()
    {
        $client = static::createClient();
        $crawler = $client->request('GET','/login');
        $form = $crawler->selectButton('Se connecter')->form([
            '_username'=>'test@test.fr',
            '_password'=>'null'
        ]);
        $client->submit($form);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('.alert-danger');

    }

    public function testSuccessLogin()
    {
        $client = static::createClient();
        $crawler = $client->request('GET','/login');
        $form = $crawler->selectButton('Se connecter')->form([
            '_username'=>'test@test.fr',
            '_password'=>'123456'
        ]);
        $client->submit($form);
        $this->assertResponseRedirects('');


    }






}