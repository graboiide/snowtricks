<?php

namespace App\Controller;

use App\Repository\CommentRepository;
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
    private $config;
    public function __construct()
    {
        //charger la config ici
    }

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
            'nbTricks' => $repo->count([]),
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
        return $this->render('home/tricks.html.twig', [
            'tricks' => $tricks
        ]);
    }

    /**
     * @Route("/tricks/{slug}",name="show_tricks")
     * @param $slug
     * @param TricksRepository $repo
     * @param CommentRepository $repoComment
     * @return Response
     */
    public function show($slug,TricksRepository $repo,CommentRepository $repoComment):Response
    {
        $figure = $repo->findOneBy(['slug'=>$slug]);
        $figureId = $figure->getId();
        return $this->render('home/show.html.twig', [
            'figure'=>$figure,
            'comments'=>$repoComment->findBy(
                ['figure'=>$figureId],
                ['id'=>'DESC'],2,0),
            'nbLoad'=>2,
            'nbComments'=>$repoComment->count(['figure'=>$figure->getId()])
        ]);
    }

    /**
     * Charge les 5 commentaires de la page
     * @Route("/comments/{page}/{figureId}",name="load_comments")
     * @param $page
     * @param $figureId
     * @param CommentRepository $commentRepository
     * @return Response
     */
    public function loadComments($page,$figureId,CommentRepository $commentRepository):Response
    {
        $comments = $commentRepository->findBy(['figure'=>$figureId],['id'=>'DESC'],2,$page * 2);
        return $this->render('home/comments.html.twig', [
            'comments'=>$comments
        ]);
    }
}
