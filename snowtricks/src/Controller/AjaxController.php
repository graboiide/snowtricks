<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use App\Repository\GroupRepository;
use App\Repository\TricksRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\NormalizableInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class AjaxController extends AbstractController
{

    /**
     * @Route("/addComment",name="add_comment")
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function addComment(EntityManagerInterface $entityManager,Request $request,TricksRepository $tricksRepository)
    {
        $comment = new Comment();
        $figure = $tricksRepository->findOneBy(['id'=>$request->request->get('idFigure')]);
        $comment
            ->setFigure($figure)
            ->setUser($this->getUser())
            ->setMessage($request->request->get('message'));
        $entityManager->persist($comment);
        $entityManager->flush();
        return $this->render('home/comments.html.twig',['comments'=>[$comment]]);
    }

    /**
     * @Route("/removeComment/{id}",name="remove_comment")
     * @param $id
     * @IsGranted("ROLE_USER")
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param CommentRepository $commentRepository
     * @return Response
     */
    public function removeComment($id,EntityManagerInterface $entityManager,Request $request,CommentRepository $commentRepository)
    {
        $comment = $commentRepository->findOneBy(['id'=>$id]);
        if($this->isUserOrAdmin($comment->getUser())){
            $entityManager->remove($comment);
            $entityManager->flush();
            return new Response('Ok');
        }

        return new Response('Error');
    }


    /**
     * @Route("/editComment",name="edit_comment")
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param CommentRepository $commentRepository
     * @return Response
     * @IsGranted("ROLE_USER")
     */
    public function editComment(EntityManagerInterface $entityManager,Request $request,CommentRepository $commentRepository)
    {

        $comment = $commentRepository->findOneBy(['id'=>$request->request->get('id')]);
        if($this->isUserOrAdmin($comment->getUser())){
            $comment->setMessage($request->request->get('message'));
            $entityManager->persist($comment);
            $entityManager->flush();
            return new Response('OK');
        }

        return new Response('Error');
    }

    /**
     * @Route("/generate/media/{type}", name="load_media")
     * @param $type
     * @return Response
     */
    public function loadTricks($type):Response
    {

        return $this->render('home/media-tmpl.html.twig',['media'=>['type'=>(int)$type],'edit'=>true]);
    }

    /**
     * @Route("/admin/upload", name="ajax_upload")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function upload(Request $request,EntityManagerInterface $manager,FileUploader $fileUploader)
    {

        if(isset($request->files)) {
            $tabExt = array('jpg', 'gif', 'png', 'jpeg');
            $uniqID = uniqid();
            $file = $request->files->get('file');
            $nameModify = 'big_'.$uniqID.'_'.$file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension());
            //$name = $file->getClientOriginalName();
            if(in_array($extension,$tabExt)){
                $fileUploader->setMaxSize(2);
                $nameModify = $fileUploader->upload($file);
            }

        }

        $response = new Response(json_encode($nameModify));
        $response->headers->set('Content-Type','application/json');
        return $response;
    }

    /**
     * @Route("/admin/group/remove/{id}", name="admin_removeGroup")
     * @IsGranted("ROLE_ADMIN")
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function removeGroup($id,EntityManagerInterface $entityManager,GroupRepository $groupRepository,Request $request)
    {
        $groupToRemove = $groupRepository->findOneBy(['id'=>$id]);
        if(!is_null($groupToRemove)){
            $entityManager->remove($groupToRemove);
            $entityManager->flush();
            return new Response('OK');
        }
        return new Response('error id');
    }


    private function isUserOrAdmin($userToCompare){
        if($userToCompare === $this->getUser() or in_array('ROLE_ADMIN',$this->getUser()->getRoles())){
            return true;
        }
        return false;
    }

}
