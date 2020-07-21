<?php


namespace App\Tests\Controller;


use App\Controller\HomeController;

use App\Entity\Role;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AdminControllerTest extends WebTestCase
{

    public function testEditDisplay()
    {
        $client = static::createClient();
        $userRepo = static::$container->get(UserRepository::class);
        $user = $userRepo->findOneBy(['email'=>'test@test.fr']);
        $client->loginUser($user);

        $client->request('GET','/admin/edit/figure-test');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}