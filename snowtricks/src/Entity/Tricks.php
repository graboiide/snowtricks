<?php

namespace App\Entity;

use App\Repository\TricksRepository;
use App\Src\Service\Slug\Slug;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=TricksRepository::class)
 * @UniqueEntity(fields={"slug"},message="Cette figure existe déja veuillez en créer une autre ou la modifier")
 * @ORM\HasLifecycleCallbacks()
 */
class Tricks
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("tricks:read")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("tricks:read")
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @Groups("tricks:read")
     */
    private $description;

    /**
     * @ORM\Column(type="date")
     * @Groups("tricks:read")
     */
    private $dateAdd;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups("tricks:read")
     */
    private $dateModif;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("tricks:read")
     */
    private $cover;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("tricks:read")
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=Media::class, mappedBy="figure", orphanRemoval=true)
     * @Groups("tricks:read")
     */
    private $medias;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="Tricks")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("tricks:read")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="figure", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\ManyToOne(targetEntity=Group::class, inversedBy="tricks")
     * @ORM\JoinColumn(nullable=false)
     *
     */
    private $family;
    private $isAuthor;

    /**
     * @return mixed
     */
    public function getIsAuthor()
    {
        return $this->isAuthor;
    }

    /**
     * @param mixed $autorizeEdit
     */
    public function setIsAuthor($autorizeEdit): void
    {
        $this->isAuthor = $autorizeEdit;
    }

    /**
     * Slug automatique
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function createSlug()
    {
        $slugify = new Slug();
        $this->slug = $slugify->slugify($this->name);
    }
    /**
     * chnage les dates
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function modifyDate()
    {
        if(is_null($this->dateAdd))
            $this->dateAdd = new DateTime('now');
        else
            $this->dateModif = new DateTime('now');
    }

    public function __construct()
    {
        $this->medias = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDateAdd(): ?\DateTimeInterface
    {
        return $this->dateAdd;
    }

    public function setDateAdd(\DateTimeInterface $dateAdd): self
    {
        $this->dateAdd = $dateAdd;

        return $this;
    }

    public function getDateModif(): ?\DateTimeInterface
    {
        return $this->dateModif;
    }

    public function setDateModif(?\DateTimeInterface $dateModif): self
    {
        $this->dateModif = $dateModif;

        return $this;
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(?string $cover): self
    {
        $this->cover = $cover;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|Media[]
     */
    public function getMedias(): Collection
    {
        return $this->medias;
    }

    public function addMedia(Media $media): self
    {
        if (!$this->medias->contains($media)) {

            $this->medias[] = $media;
            $media->setFigure($this);
        }

        return $this;
    }

    public function removeMedia(Media $media): self
    {
        if ($this->medias->contains($media)) {
            $this->medias->removeElement($media);
            // set the owning side to null (unless already changed)
            if ($media->getFigure() === $this) {
                $media->setFigure(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setFigure($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getFigure() === $this) {
                $comment->setFigure(null);
            }
        }

        return $this;
    }

    public function getFamily(): ?Group
    {
        return $this->family;
    }

    public function setFamily(?Group $family): self
    {
        $this->family = $family;

        return $this;
    }

    /**
     * Retourne toutes les images
     * @return array
     */
    public function getImages()
    {
        $images = [];
        /**
         * @var Media $media
         */
        foreach ($this->medias as $media){
            if($media->getType() === 0)
                $images[]=$media;
        }
        return $images;
    }
    /**
     * Retourne toutes les images
     * @return array
     */
    public function getmovies()
    {
        $movies = [];
        /**
         * @var Media $media
         */
        foreach ($this->medias as $media){
            if($media->getType() === 1){
                $movies[]=$media;
            }

        }
        return $movies;
    }

}
