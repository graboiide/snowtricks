<?php

namespace App\Controller;

use App\Form\TricksType;
use App\Repository\TricksRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin/edit/{id}", name="admin_edit")
     * @param $id
     * @param TricksRepository $tricksRepository
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    public function edit($id,TricksRepository $tricksRepository,EntityManagerInterface $manager,Request $request)
    {
        $figure = $tricksRepository->findOneBy(['id'=>$id]);
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
     */
    public function remove($id)
    {

        return $this->render('admin/edit_tricks.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
}
