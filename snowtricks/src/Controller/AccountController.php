<?php

namespace App\Controller;

use App\Entity\ChangePassword;
use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class AccountController extends AbstractController
{
    /**
     * @Route("/login", name="account_login")
     * @param AuthenticationUtils $utils
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $validate = true;
        $error = $utils->getLastAuthenticationError();
        if(!is_null($error) && $error->getMessageKey() === 'no-validate')
            $validate = false;

        return $this->render('account/login.html.twig',[
            "error"=> $error !== null,
            "username"=> $utils->getLastUsername(),
            "noValidate"=> !$validate
        ]);
    }

    /**
     * @Route("/logout", name="account_logout")
     */
    public function logout()
    {
    }

    /**
     * @Route("/registration",name="account_registration")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $encoder
     * @param TokenGeneratorInterface $generator
     * @return Response
     */
    public function registration(Request $request,EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder,TokenGeneratorInterface $generator)
    {
        $user = new User();
        $token = $generator->generateToken();

        $form = $this->createForm(RegistrationType::class,$user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $user->setHash($encoder->encodePassword($user,$user->getHash()));
            $user->setIsValidate(0);
            $user->setToken($token);
            $entityManager->persist($user);
            $entityManager->flush();
            $this->sendConfirmation($token,$user->getId(),$user->getEmail());
            $this->addFlash('success',"Votre compte à été créer. Veuillez l'activer à l'aide du mail de confirmation que vous avez du recevoir");
            return $this->redirectToRoute('account_login');
        }
        return $this->render('account/registration.html.twig',['form'=>$form->createView()]);
    }
    public function sendConfirmation($token,$id,$email)
    {
        $headers = 'From: '.'gregcodeur@gmail.com' . "\r\n" .
            'Reply-To:'.'graboiide@gmail.com' . "\r\n".
            'MIME-Version: 1.0' . "\r\n".
            'Content-type: text/html; charset=iso-8859-1' . "\r\n" ;

        $message =
            'Merci pour votre inscription cliquez sur ce lien pour confirmer votre inscription 
            <a href="https://www.oc-p6.gregcodeur.fr/confirm/'.$token.'/'.$id.'"> >> Activer mon compte ! << </a>';
        mail($email,'Confirmez votre compte snowtricks !',$message,$headers);

    }
    public function sendChangePassword($token,$id,$email)
    {
        $headers = 'From: '.'gregcodeur@gmail.com' . "\r\n" .
            'Reply-To:'.'graboiide@gmail.com' . "\r\n".
            'MIME-Version: 1.0' . "\r\n".
            'Content-type: text/html; charset=iso-8859-1' . "\r\n" ;

        $message =
            'Vous avez demandé de changer votre mot de passe, cliquez sur le lien 
            <a href="https://www.oc-p6.gregcodeur.fr/forget/'.$token.'/'.$id.'"> >> Changer mon mot de passe << </a> afin de le modifier !';
        mail($email,'Snowtricks : Mot de passe oublié ',$message,$headers);

    }

    /**
     * @Route("/confirm/{token}/{id}",name="account_confirm")
     * @param $token
     * @param $id
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @return RedirectResponse
     */
    public function confirmAccount($token,$id,EntityManagerInterface $entityManager,UserRepository $userRepository)
    {
        $user = $userRepository->findOneBy(["id"=>$id]);
        if(!is_null($user) &&  $token === $user->getToken()){
            $user->setIsValidate(1);
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success','Votre compte à bienété activé, vous pouvez dés a présent vous connecter !');
            return $this->redirectToRoute('account_login');

        }
        $this->addFlash(
            'danger',
            'Une erreur est survenu, vous pouvez créer un compte et rejoindre notre communauté, si le probleme persiste veuillez contacter l\'administrateur');
        return $this->redirectToRoute('account_registration');


    }

    /**
     * @Route("/forget",name="account_forget")
     * @param Request $request
     * @param UserRepository $userRepository
     * @param TokenGeneratorInterface $tokenGenerator
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function forgetPassword(Request $request,UserRepository $userRepository,TokenGeneratorInterface $tokenGenerator,EntityManagerInterface $entityManager)
    {

        $message = null;
        if($request->getMethod() === 'POST'){
           $user = $userRepository->findOneBy(['email'=>$request->request->get('email')]);
           if($user){
               $newToken = $tokenGenerator->generateToken();
               $user->setToken($newToken);
               $entityManager->persist($user);
               $entityManager->flush();
               $this->sendChangePassword($newToken,$user->getId(),$user->getEmail());
               $message ='Un email avec les instructions de changement de mot de passe vous à été envoyer';

           }else{
               $message ='Ce compte est inexistant';
           }

        }
        return $this->render('account/forget.html.twig',['message'=>$message]);
    }

    /**
     * @Route("/forget/{token}/{id}",name="account_changePass")
     * @param $id
     * @param $token
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function changePassword($id,$token,UserRepository $userRepository,EntityManagerInterface $entityManager,Request $request,UserPasswordEncoderInterface $encoder)
    {
        $session = $request->getSession();
        $user = $userRepository->findOneBy(['id'=>$id]);
        //Si il s'agit bien du bon utilisateur on l'enregistre en session
        if($user && $user->getToken() === $token)
            $session->set('userTmp',$user);
        else
            return $this->redirectToRoute('account_login');

        $changePassword = new ChangePassword();
        $form = $this->createForm(ChangePasswordType::class,$changePassword);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid() && !is_null($session->get('userTmp'))){
            /**
             * @var User $user
             */
            $user = $session->get('userTmp');
            $user->setToken('empty');
            $user->setHash($encoder->encodePassword($user,$changePassword->getPassword()));
            $entityManager->persist($user);
            $entityManager->flush();
            $session->remove('userTmp');
            $this->addFlash('success','Votre mot de passe à bien été changé');
            return $this->redirectToRoute('account_login');
        }

        return $this->render('account/change-password.html.twig',['form'=>$form->createView()]);
    }
}
