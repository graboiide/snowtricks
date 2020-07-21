<?php


namespace App\Controller;


use App\Entity\Tricks;
use App\Entity\User;
use App\Repository\ConfigRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Twig\Environment;

class BackController extends AbstractController
{
    protected $config;
    public function __construct(ConfigRepository $configRepository,Environment $twig)
    {
        $this->config = $configRepository->findBy([],['id'=>'desc'])[0];
        $twig->addGlobal('Config',$this->config);

    }

    /**
     * Verifie que l'utilisateur est autorisé a modifié la figure
     * @param Tricks $figure
     * @return bool
     */
    protected function isUserGranted(User $user)
    {
       return (($this->config->getProtectLevel() === 0 || $this->getUser()->AsRole('ROLE_ADMIN')) ||
            ($this->config->getProtectLevel() === 1 && $user === $this->getUser()));
    }

    protected function userIsAuthorizeToAccess(User $user)
    {

        if(!$this->isUserGranted($user))
            throw new AccessDeniedException('Accées refusé');

    }

    protected function findEditableTricks($tricks)
    {

        /** * @var Tricks $figure */
        foreach ($tricks as $figure)
            $figure->setIsEditable($this->getUser() && $this->isUserGranted($figure->getUser()));

        return  $tricks;
    }
    protected function checkFigureIsEditable(Tricks $figure)
    {
        $figure->setIsEditable($this->getUser() && $this->isUserGranted($figure->getUser()));

        return  $figure;
    }


}