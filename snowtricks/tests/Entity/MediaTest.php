<?php


namespace App\Tests\Entity;



use App\Entity\Group;
use App\Entity\Media;
use App\Entity\Tricks;
use App\Entity\User;
use DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class MediaTest extends KernelTestCase
{
   use AssertHasError;



   public function testValidMedia(){
       $this->assertHasError($this->createEntity(),0);
   }

    public function testNotValidMedia(){

        $figure = $this->createEntity()->setCaption('');
        $this->assertHasError($figure,1);
    }
    public function testNotBlankCaption(){

        $figure = $this->createEntity()->setCaption('');
        $this->assertHasError($figure,1);
    }
    public function testNotBlankUrl(){

        $figure = $this->createEntity()->setUrl('');
        $this->assertHasError($figure,1);
    }


    private function createEntity(){
        $media = new Media();
        $media
            ->setFigure(new Tricks())
            ->setUrl('/test/url')
            ->setCaption('Un titre de test')
            ->setType(0);
        return $media;
    }
}