<?php

namespace App\Controller;

use App\Entity\Comment;
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
        $tricks = $this->checkPermEditTricks($repo->findBy([],['id'=>'DESC'],$nbTricksDisplay,0));

        return $this->render('home/index.html.twig', [
            'tricks' => $tricks,
            'nbTricks' => $repo->count([]),
            'nbLoad' => $nbTricksDisplay
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
        $tricks = $this->checkPermEditTricks($repo->findBy([],['id'=>'DESC'],12,$page * 12));
        return $this->render('home/tricks.html.twig', [
            'tricks' => $tricks
        ]);
    }

    /**
     * @Route("/tricks/{slug}",name="show_tricks")
     * @param $slug
     * @param TricksRepository $repo
     * @param CommentRepository $repoComment
     * @param Request $request
     * @return Response
     */
    public function show($slug, TricksRepository $repo, CommentRepository $repoComment):Response
    {
        $figure = $repo->findOneBy(['slug'=>$slug]);
        $figure = $this->checkPermEditTricks([$figure])[0];
        $comment = new Comment();
        $formComment = $this->createForm(CommentType::class,$comment);

        $figureId = $figure->getId();
        return $this->render('home/show.html.twig', [
            'figure'=>$figure,
            'comments'=>$repoComment->findBy(
                ['figure'=>$figureId],
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
        $comments = $commentRepository->findBy(['figure'=>$figureId],['id'=>'DESC'],2,$page * 2);
        return $this->render('home/comments.html.twig', [
            'comments'=>$comments
        ]);
    }
    /**
     * Retourne les figures avec l'autorisation d'édition
     * @param $tricks
     * @return mixed
     */
    protected function checkPermEditTricks($tricks)
    {
        /**
         * @var Tricks $figure
         */
        foreach ($tricks as $figure){
            $figure->setIsAuthor(false);
            if ($this->getUser() && $this->isAuthorGranted($figure) )
                $figure->setIsAuthor(true);
        }
        return $tricks;
    }

}
