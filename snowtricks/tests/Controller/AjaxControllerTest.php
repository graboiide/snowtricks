<?php


namespace App\Tests\Controller;


use App\Controller\HomeController;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AjaxControllerTest extends WebTestCase
{

    public function testLoadTricksInAjax()
    {
        $client = static::createClient();
        $client->request('GET','/load/0');
        $this->assertSelectorExists('.card');

    }
    public function testLoadTemplateImgCard()
    {
        $client = static::createClient();
        $client->request('GET','/generate/media/0');
        $this->assertSelectorExists('.img-fluid');
    }
    public function testLoadTemplateMovieCard()
    {
        $client = static::createClient();
        $client->request('GET','/generate/media/1');
        $this->assertSelectorExists('.embed-responsive');
    }


}