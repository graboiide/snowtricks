<?php


namespace App\Tests\Entity;


use App\Entity\Tricks;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Validator\ConstraintViolation;

trait AssertHasError
{
    private function assertHasError($figure,$nbError){
        self::bootKernel();
        $errors = self::$container->get('validator')->validate($figure);
        $messages =[];
        /**
         * @var ConstraintViolation $error
         */
        foreach ($errors as $error){
            $messages[]=$error->getPropertyPath().'>>'.$error->getMessage();
        }
        $this->assertCount($nbError,$errors,implode(' , ',$messages));
    }
}