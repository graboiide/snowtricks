<?php

namespace App\Entity;

use App\Repository\GroupRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=GroupRepository::class)
 * @UniqueEntity(fields={"name"},message="Un groupe avec le même nom existe déja")
 * @ORM\Table(name="`group`")
 */
class Group
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
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Tricks::class, mappedBy="family", orphanRemoval=true)
     */
    private $tricks;

    public function __toString()
    {
        return $this->name;
    }
    public function __construct()
    {
        $this->tricks = new ArrayCollection();
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

    /**
     * @return Collection|Tricks[]
     */
    public function getTricks(): Collection
    {
        return $this->tricks;
    }

    public function addTrick(Tricks $trick): self
    {
        if (!$this->tricks->contains($trick)) {
            $this->tricks[] = $trick;
            $trick->setFamily($this);
        }

        return $this;
    }

    public function asTricks()
    {
        if(count($this->tricks)>0)
            return true;
        return false;
    }

    public function removeTrick(Tricks $trick): self
    {
        if ($this->tricks->contains($trick)) {
            $this->tricks->removeElement($trick);
            // set the owning side to null (unless already changed)
            if ($trick->getFamily() === $this) {
                $trick->setFamily(null);
            }
        }

        return $this;
    }
}
