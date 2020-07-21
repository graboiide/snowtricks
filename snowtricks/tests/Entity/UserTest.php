<?php


namespace App\Tests\Entity;



use App\DataFixtures\TricksFixtures;
use App\Entity\Group;
use App\Entity\Media;
use App\Entity\Tricks;
use App\Entity\User;
use DateTime;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class UserTest extends KernelTestCase
{
   use AssertHasError;

   public function testValidUser(){

       $this->assertHasError($this->createEntity(),0);
   }

    public function testNotValidUser(){
        $user = $this->createEntity()->setHash('f');
        $this->assertHasError($user,1);
    }

    public function testValidUserName(){
       $user = $this->createEntity()->setName('pseudovalid11_9');
        $this->assertHasError($user,0);
    }
    public function testNoValidUserName(){
        $user = $this->createEntity()->setName('frédy !!!');
        $this->assertHasError($user,1);
    }
    public function testEqualToUserName(){
        $user = $this->createEntity()->setEmail('gregcodeur@gmail.com');
        $this->assertHasError($user,1);
    }

    public function testPasswordValid(){
        $user = $this->createEntity()->setHash('Pass!7Valid');
        $this->assertHasError($user,0);
    }
    public function testPasswordNotMaj(){
        $user = $this->createEntity()->setHash('pass!7valid');
        $this->assertHasError($user,1);
    }
    public function testPasswordNotMin(){
        $user = $this->createEntity()->setHash('PASS!7VALID');
        $this->assertHasError($user,1);
    }
    public function testPasswordNotNumber(){
        $user = $this->createEntity()->setHash('Pass!Valid');
        $this->assertHasError($user,1);
    }
    public function testPasswordNotSpecialsCharacters(){
        $user = $this->createEntity()->setHash('Pass7Valid');
        $this->assertHasError($user,1);
    }
    public function testPasswordNotForbiddenCharacters(){
        $user = $this->createEntity()->setHash('&{PassValid');
        $this->assertHasError($user,1);
    }
    public function testPasswordTooShort(){
        $user = $this->createEntity()->setHash('ss!7V');
        $this->assertHasError($user,1);
    }
    public function testPasswordTooLong(){
        $user = $this->createEntity()->setHash('Pass!7ValidPass!7ValidPass!7ValidPass!7ValidPass!7ValidPass!7Valid');
        $this->assertHasError($user,1);
    }

    private function createEntity(){
        $user = new User();
        $user
            ->setName('Usertest')
            ->setHash('Pass!7Valid')
            ->setToken('token_test')
            ->setIsValidate(1)
            ->setEmail('test2@test.fr')
            ->setDescription('fff')
            ->setAvatar('uploads/avatar.jpg');
        return $user;
    }
}