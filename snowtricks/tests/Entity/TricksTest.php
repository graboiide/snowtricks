<?php


namespace App\Tests\Entity;



use App\Entity\Group;
use App\Entity\Tricks;
use App\Entity\User;
use DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class TricksTest extends KernelTestCase
{
   use AssertHasError;
   public function testSlug(){
       $tricks = new Tricks();
       $tricks->setName('un sluG à testé !');
       $tricks->createSlug();
       $this->assertSame('un-slug-a-teste',$tricks->getSlug());
   }


   public function testValidTricks(){
       $this->assertHasError($this->createEntity(),0);
   }

    public function testNotValidTricks(){
        $figure = $this->createEntity()->setDescription('')->setName('de');
        $this->assertHasError($figure,2);
    }

    public function testEqualToNameTricks(){
        $figure = $this->createEntity()->setName('figure_test');
        $this->assertHasError($figure,1);
    }

    private function createEntity(){
        $figure = new Tricks();
        $figure
            ->setName('un sluG à testé !')
            ->setUser(new User())
            ->setDateAdd(new DateTime('now'))
            ->setFamily(new Group())
            ->setDescription('description test')
            ->setCover('cover')
        ;
        $figure->createSlug();
        return $figure;
    }
}