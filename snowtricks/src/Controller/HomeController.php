<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Group;
use App\Entity\Tricks;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\ConfigRepository;
use App\Repository\TricksRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class HomeController extends BackController
{


    /**
     * @Route("/", name="home")
     * @param TricksRepository $repo
     * @return Response
     */
    public function index(TricksRepository $repo):Response
    {
        $nbTricksDisplay = $this->config->getNbTricksDisplay();
        $tricks = $this->findEditableTricks($repo->findBy([],['id'=>'DESC'],$nbTricksDisplay,0));



        return $this->render('home/index.html.twig', [
            'tricks' => $tricks,
            'nbTricks' => $repo->count([]),
            'nbLoad' => $nbTricksDisplay
        ]);
    }



    /**
     * @Route("/tricks/{slug}",name="show_tricks")
     * @param $slug
     * @param Tricks $figure
     * @param CommentRepository $repoComment
     * @return Response
     */
    public function show($slug, Tricks $figure, CommentRepository $repoComment):Response
    {
        $comment = new Comment();
        $formComment = $this->createForm(CommentType::class,$comment);
        $figure = $this->checkFigureIsEditable($figure);

        return $this->render('home/show.html.twig', [
            'figure'=>$figure,
            'comments'=>$repoComment->findBy(
                ['figure'=>$figure->getId()],
                ['id'=>'DESC'],$this->config->getNbMessagesDisplay(),0),
            'nbLoad'=>$this->config->getNbMessagesDisplay(),
            'nbComments'=>$repoComment->count(['figure'=>$figure->getId()]),
            'form'=>$formComment->createView()
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
        $comments = $commentRepository->findBy(
            ['figure'=>$figureId],['id'=>'DESC'],
            $this->config->getNbMessagesDisplay(),
            $page * $this->config->getNbMessagesDisplay()
        );
        return $this->render('home/comments.html.twig', [
            'comments'=>$comments
        ]);
    }
    /**
     * Marque les figures editable pour l'utilisateur
     * @param $tricks
     * @return mixed
     */


}
