<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Repository\TricksRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
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

}
