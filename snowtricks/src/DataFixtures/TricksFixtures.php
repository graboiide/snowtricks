<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Group;
use App\Entity\Media;
use App\Entity\Tricks;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class TricksFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // groupe de figures
        $groups=['grabs','rotation','flips','slides','one foot tricks','old school'];
        $entityGroups = [];
        foreach ($groups as $group)
        {
            $entityGroup = new Group();
            $entityGroup->setName($group);
            $entityGroups[]= $entityGroup;
            $manager->persist($entityGroup);
        }
        $faker = Factory::create('fr_FR');
        //users
        $users = [];
        for ($i = 0 ; $i < 10 ; $i++){
            $user = new User();
            $user->setName($faker->name())
                ->setDescription($faker->sentence())
                ->setIsValidate(1)
                ->setAvatar('https://picsum.photos/64/64?random='.$i)
                ->setEmail($faker->email)
                ->setHash('123456')
                ->setToken(uniqid());
            $manager->persist($user);
            $users[] = $user;
        }
        //tricks
        for ($i =0 ; $i<60 ; $i++){
            $figure = new Tricks();
            $startDate = mt_rand(-5,-1);
            $figure
                ->setName($faker->sentence(3))
                //lorempixel a la facheuse tendance a souvent moulinet voir ne pas fonctionner je remplace par picsum plus fiable
                ->setCover('https://picsum.photos/840/480?random='.$i)
                ->setDescription('<p>'.join('</p><p>',$faker->paragraphs()).'</p>')
                ->setDateAdd($faker->dateTimeBetween($startDate.'years'))
                ->setSlug($faker->slug)
                ->setFamily($entityGroups[mt_rand(0,5)])
                ->setUser($users[mt_rand(0,9)]);
            $manager->persist($figure);
            //comments
            for ($j =0 ; $j < mt_rand(0,20) ; $j++){
                $comment = new Comment();
                $comment
                    ->setDate($faker->dateTimeBetween($startDate.'years'))
                    ->setMessage($faker->sentence(12))
                    ->setFigure($figure);
                $manager->persist($comment);
            }
            //images
            for ($j =0 ; $j < mt_rand(0,5) ; $j++){
                $media = new Media();
                $media
                    ->setType(0)
                    ->setCaption($faker->sentence(3))
                    ->setFigure($figure)
                    ->setUrl('https://picsum.photos/840/480?random='.$j);
                $manager->persist($media);
            }
            //movies
            for ($j =0 ; $j < mt_rand(0,2) ; $j++){
                $media = new Media();
                $media
                    ->setType(1)
                    ->setCaption($faker->sentence(3))
                    ->setFigure($figure)
                    ->setUrl('https://youtu.be/tHHxTHZwFUw');
                $manager->persist($media);
            }
        }

        $manager->flush();
    }
}
