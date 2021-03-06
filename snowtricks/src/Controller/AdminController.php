<?php

namespace App\Controller;

use App\Entity\Config;
use App\Entity\Group;
use App\Entity\Tricks;
use App\Form\ConfigType;
use App\Form\GroupType;
use App\Form\TricksType;
use App\Repository\ConfigRepository;
use App\Repository\GroupRepository;
use App\Repository\TricksRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AdminController extends BackController
{


    /**
     * @Route("/admin/edit/{slug}", name="admin_edit")
     * @Route("/admin/add", name="admin_add")
     * @IsGranted("ROLE_USER")
     * @param $slug
     * @param TricksRepository $tricksRepository
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     * @throws Exception
     */
    public function edit($slug = null,TricksRepository $tricksRepository,EntityManagerInterface $manager,Request $request)
    {
        if(!is_null($slug)){
            $figure = $tricksRepository->findOneBy(['slug'=>$slug]);
        } else{
            $figure = new Tricks();
            $figure->setUser($this->getUser());
        }
        //dd($figure->getImages());
        $form = $this->createForm(TricksType::class,$figure);
        $form->handleRequest($request);

        //si pas autorisé redirect en retourne un 403
        $this->userIsAuthorizeToAccess($figure->getUser());

        if($form->isSubmitted() && $form->isValid()){
            foreach ($figure->getMedias() as $media) {
                $media->setFigure($figure);
                $manager->persist($media);
            }
            $this->flashMessage($figure);
            $manager->persist($figure);
            $manager->flush();

            return $this->redirectToRoute('home');
        }
        return $this->render('admin/edit_tricks.html.twig', [
            'figure' => $figure,
            'form'=>$form->createView()
        ]);
    }

    /**
     * Permet simplement d'imprimer le bon message flash modifier ou ajouter
     * @param Tricks $figure
     */
    private function flashMessage(Tricks $figure)
    {
        if(is_null($figure->getId()))
            $this->addFlash('success','Figure ajoutée');
        else
            $this->addFlash('success','Figure modifiée');
    }

    /**
     * @Route("/admin/remove/{id}/{redirect}", name="admin_delete")
     * @IsGranted("ROLE_USER")
     * @param $id
     * @param bool $redirect
     * @param EntityManagerInterface $entityManager
     * @param TricksRepository $tricksRepository
     * @return RedirectResponse|Response
     */
    public function remove($id,$redirect = false,EntityManagerInterface $entityManager,TricksRepository $tricksRepository)
    {

        $figure = $tricksRepository->findOneBy(['id'=>$id]);
        $this->userIsAuthorizeToAccess($figure->getUser());

        $entityManager->remove($figure);
        $entityManager->flush();
        if(!$redirect)
            return new Response('OK');

        $this->addFlash('success','La figure '.$figure->getName().' est supprimée');
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/admin/config", name="admin_config")
     * @IsGranted("ROLE_ADMIN")
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function config(EntityManagerInterface $entityManager,ConfigRepository $configRepository,Request $request)
    {
        //on recupere la config
        $config = $configRepository->findBy([],['id'=>'desc'])[0];
        $form = $this->createForm(ConfigType::class,$config);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($config);
            $entityManager->flush();
            $this->addFlash('success','Configuration modifié');
        }
        return $this->render('admin/config.html.twig',['form'=>$form->createView()] );
    }

    /**
     * @Route("/admin/group/{id}", name="admin_group")
     * @IsGranted("ROLE_ADMIN")
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function editGroup($id = null,EntityManagerInterface $entityManager,GroupRepository $groupRepository,Request $request)
    {

        //liste de tous les group
        $groups = $groupRepository->findBy([],['id'=>'desc']);
        if(is_null($id)){
            $group = new Group();
        }else
            $group = $groupRepository->findOneBy(['id'=>$id]);
        $modified = !is_null($id);
        $formGroup = $this->createForm(GroupType::class,$group);
        $formGroup->handleRequest($request);
        if($formGroup->isSubmitted() && $formGroup->isValid()){
            $entityManager->persist($group);
            $entityManager->flush();
            return $this->redirectToRoute('admin_group');
        }
        return $this->render('admin/group-edit.html.twig',[
            'formGroup'=>$formGroup->createView(),
            'groups'=>$groups,
            'modified'=>!is_null($id)]);
    }


}
