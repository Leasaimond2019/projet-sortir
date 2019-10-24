<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\VilleRepository")
 */
class Ville
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $nom_ville;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $code_postal;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Lieu", mappedBy="no_ville", cascade={"remove"})
     */
    private $lieus;

    public function __construct()
    {
        $this->lieus = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomVille(): ?string
    {
        return $this->nom_ville;
    }

    public function setNomVille(string $nom_ville): self
    {
        $this->nom_ville = $nom_ville;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->code_postal;
    }

    public function setCodePostal(string $code_postal): self
    {
        $this->code_postal = $code_postal;

        return $this;
    }

    /**
     * @return Collection|Lieu[]
     */
    public function getLieus(): Collection
    {
        return $this->lieus;
    }

    public function addLieus(Lieu $lieus): self
    {
        if (!$this->lieus->contains($lieus)) {
            $this->lieus[] = $lieus;
            $lieus->setNoVille($this);
        }

        return $this;
    }

    public function removeLieus(Lieu $lieus): self
    {
        if ($this->lieus->contains($lieus)) {
            $this->lieus->removeElement($lieus);
            // set the owning side to null (unless already changed)
            if ($lieus->getNoVille() === $this) {
                $lieus->setNoVille(null);
            }
        }

        return $this;
    }
}
