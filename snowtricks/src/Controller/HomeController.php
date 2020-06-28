<?php

namespace App\Controller;

use App\Repository\TricksRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     * @param TricksRepository $repo
     * @return Response
     */
    public function index(TricksRepository $repo):Response
    {
        $tricks = $repo->findBy([],['id'=>'DESC'],12,0);
        return $this->render('home/index.html.twig', [
            'tricks' => $tricks,
            'nbTricks' => $repo->count([])-12,
            'nbLoad' => 12
        ]);
    }

    /**
     * @Route("/load/{page}", name="load_tricks")
     * @param $page
     * @param TricksRepository $repo
     * @return Response
     */
    public function loadTricks($page,TricksRepository $repo):Response
    {
        $tricks = $repo->findBy([],['id'=>'DESC'],12,$page * 12);
        return $this->render('home/test.html.twig', [
            'tricks' => $tricks
        ]);
    }
}
