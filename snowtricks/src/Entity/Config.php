<?php

namespace App\Entity;

use App\Repository\ConfigRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ConfigRepository::class)
 */
class Config
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbTricksDisplay;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $emailAdmin;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbMessagesDisplay;

    /**
     * @ORM\Column(type="integer")
     */
    private $protectLevel;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNbTricksDisplay(): ?int
    {
        return $this->nbTricksDisplay;
    }

    public function setNbTricksDisplay(int $nbTricksDisplay): self
    {
        $this->nbTricksDisplay = $nbTricksDisplay;

        return $this;
    }

    public function getEmailAdmin(): ?string
    {
        return $this->emailAdmin;
    }

    public function setEmailAdmin(string $emailAdmin): self
    {
        $this->emailAdmin = $emailAdmin;

        return $this;
    }

    public function getNbMessagesDisplay(): ?int
    {
        return $this->nbMessagesDisplay;
    }

    public function setNbMessagesDisplay(int $nbMessagesDisplay): self
    {
        $this->nbMessagesDisplay = $nbMessagesDisplay;

        return $this;
    }

    public function getProtectLevel(): ?int
    {
        return $this->protectLevel;
    }

    public function setProtectLevel(int $protectLevel): self
    {
        $this->protectLevel = $protectLevel;

        return $this;
    }
}
