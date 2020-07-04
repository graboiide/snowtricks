<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=MediaRepository::class)
 */
class Media
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\Column(type="integer")
     * @Groups("tricks:read")
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $caption;

    /**
     * @ORM\ManyToOne(targetEntity=Tricks::class, inversedBy="medias")
     * @ORM\JoinColumn(nullable=false)
     */
    private $figure;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function setCaption(string $caption): self
    {
        $this->caption = $caption;

        return $this;
    }

    public function getFigure(): ?Tricks
    {
        return $this->figure;
    }

    public function setFigure(?Tricks $figure): self
    {
        $this->figure = $figure;

        return $this;
    }


}
