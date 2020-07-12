<?php

namespace App\Controller;

use App\Entity\Tricks;
use App\Form\TricksType;
use App\Repository\TricksRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/edit/{id}", name="admin_edit")
     * @Route("/admin/add", name="admin_add")
     * @IsGranted("ROLE_USER")
     * @param $id
     * @param TricksRepository $tricksRepository
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function edit($id = null,TricksRepository $tricksRepository,EntityManagerInterface $manager,Request $request)
    {

        if(!is_null($id)){
            $figure = $tricksRepository->findOneBy(['id'=>$id]);
            $figure->setDateModif(new DateTime('now'));
        }
        else{
            $figure = new Tricks();
            $figure->setDateAdd(new DateTime('now'));
            $figure->setSlug('dfr');
            $figure->setUser($this->getUser());
        }
        //dd($figure->getImages());
        $form = $this->createForm(TricksType::class,$figure);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            foreach ($figure->getMedias() as $media) {
                $media->setFigure($figure);
                $manager->persist($media);
            }
            $manager->persist($figure);
            $manager->flush();
            return $this->redirectToRoute('show_tricks',['slug'=>$figure->getSlug()]);
        }
        return $this->render('admin/edit_tricks.html.twig', [
            'figure' => $figure,
            'form'=>$form->createView()
        ]);
    }
    /**
     * @Route("/admin/remove/{id}", name="admin_delete")
     * @IsGranted("ROLE_USER")
     */
    public function remove($id)
    {
        return $this->render('admin/edit_tricks.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
}
