<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Group;
use App\Entity\Media;
use App\Entity\Role;
use App\Entity\Tricks;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class TricksFixtures extends Fixture
{
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->encoder = $passwordEncoder;
    }

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
        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);
        $userAdmin = new User();
        $userAdmin
            ->setName('admin')
            ->setHash($this->encoder->encodePassword($userAdmin,'admin'))
            ->setToken(uniqid())
            ->setEmail('gregcodeur@gmail.com')
            ->setIsValidate(1)
            ->addUserRole($adminRole);
        $manager->persist($userAdmin);
        $users = [];

        for ($i = 0 ; $i < 10 ; $i++){
            $genres = ['male','female'];
            $genre = $faker->randomElement($genres);
            $avatar = 'https://randomuser.me/api/portraits/'.($genre == 'male' ? 'men' : 'women').'/'.mt_rand(0,99).'.jpg';

            $user = new User();
            $user->setName($faker->firstName($genre))
                ->setDescription($faker->sentence())
                ->setIsValidate(1)
                ->setAvatar($avatar)
                ->setEmail($faker->email)
                ->setHash($this->encoder->encodePassword($user,'123456'))
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

                $date = $faker->dateTimeBetween($startDate.'years');

                $comment
                    ->setDate($date)
                    ->setMessage($faker->sentence(12))
                    ->setFigure($figure)
                    ->setUser($users[mt_rand(0,9)]);

                $manager->persist($comment);
            }
            //images
            for ($j =0 ; $j < mt_rand(10,20) ; $j++){
                $media = new Media();
                $media
                    ->setType(0)
                    ->setCaption($faker->sentence(3))
                    ->setFigure($figure)
                    ->setUrl('https://picsum.photos/840/480?random='.mt_rand(0,100));
                $manager->persist($media);
            }
            //movies
            for ($j =0 ; $j < mt_rand(10,20) ; $j++){
                $media = new Media();
                $media
                    ->setType(1)
                    ->setCaption($faker->sentence(3))
                    ->setFigure($figure)
                    ->setUrl('https://youtube.com/embed/tHHxTHZwFUw');
                $manager->persist($media);
            }
        }

        $manager->flush();
    }
}
