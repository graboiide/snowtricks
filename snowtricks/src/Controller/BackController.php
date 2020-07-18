<?php


namespace App\Controller;


use App\Entity\Tricks;
use App\Repository\ConfigRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Twig\Environment;

class BackController extends AbstractController
{
    protected $config;
    public function __construct(ConfigRepository $configRepository,Environment $twig)
    {
        $this->config = $configRepository->findBy([],['id'=>'desc'])[0];
        $twig->addGlobal('lvlProtection',$this->config->getProtectLevel());
    }

    /**
     * Verifie que l'utilisateur est autorisé a modifié la figure
     * @param Tricks $figure
     * @return bool
     */
    protected function isAuthorGranted(Tricks $figure)
    {
        if ((($this->config->getProtectLevel() === 0 || $this->getUser()->asRole('ROLE_ADMIN')) ||
            ($this->config->getProtectLevel() === 1 && $figure->getUser() === $this->getUser())))
            return true;
        return false;
    }


}