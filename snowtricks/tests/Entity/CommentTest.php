<?php


namespace App\Tests\Entity;



use App\Entity\Comment;
use App\Entity\Group;
use App\Entity\Media;
use App\Entity\Tricks;
use App\Entity\User;
use DateTime;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class CommentTest extends KernelTestCase
{
   use AssertHasError;



   public function testValidMedia(){
       $this->assertHasError($this->createEntity(),0);
   }

    public function testNotValidMedia(){

        $comment = $this->createEntity()->setMessage('');
        $this->assertHasError($comment,1);
    }

    private function createEntity(){
        $comment = new Comment();
        $comment
            ->setFigure(new Tricks())
            ->setUser(new User())
            ->setMessage('message')
            ->setDate(new DateTime('now'));
        return $comment;
    }
}