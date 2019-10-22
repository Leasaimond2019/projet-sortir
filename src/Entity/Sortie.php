<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Integer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SortieRepository")
 */
class Sortie
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
    private $nom;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_debut;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duree;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_cloture;

    /**
     * @ORM\Column(type="integer")
     */
    private $nb_inscription_max;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $etat_sortie;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    private $url_photo;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Etat", inversedBy="sorties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $no_etat;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Lieu", inversedBy="sorties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $no_lieu;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="sorties")
     */
    private $no_organisateur;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Site")
     * @ORM\JoinColumn(nullable=false)
     */
    private $no_site;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Inscription", mappedBy="no_sortie")
     */
    private $no_inscription;

    public function __construct()
    {
        $this->no_inscription = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): self
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateCloture(): ?\DateTimeInterface
    {
        return $this->date_cloture;
    }

    public function setDateCloture(\DateTimeInterface $date_cloture): self
    {
        $this->date_cloture = $date_cloture;

        return $this;
    }

    public function getNbInscriptionMax(): ?int
    {
        return $this->nb_inscription_max;
    }

    public function setNbInscriptionMax(int $nb_inscription_max): self
    {
        $this->nb_inscription_max = $nb_inscription_max;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getEtatSortie(): ?int
    {
        return $this->etat_sortie;
    }

    public function setEtatSortie(?int $etat_sortie): self
    {
        $this->etat_sortie = $etat_sortie;

        return $this;
    }

    public function getUrlPhoto(): ?string
    {
        return $this->url_photo;
    }

    public function setUrlPhoto(?string $url_photo): self
    {
        $this->url_photo = $url_photo;

        return $this;
    }

    public function getNoEtat(): ?Etat
    {
        return $this->no_etat;
    }

    public function setNoEtat(?Etat $no_etat): self
    {
        $this->no_etat = $no_etat;

        return $this;
    }

    public function getNoLieu(): ?Lieu
    {
        return $this->no_lieu;
    }

    public function setNoLieu(?Lieu $no_lieu): self
    {
        $this->no_lieu = $no_lieu;

        return $this;
    }

    public function getNoOrganisateur(): ?User
    {
        return $this->no_organisateur;
    }

    public function setNoOrganisateur(?User $no_organisateur): self
    {
        $this->no_organisateur = $no_organisateur;

        return $this;
    }

    public function getNoSite(): ?Site
    {
        return $this->no_site;
    }

    public function setNoSite(?Site $no_site): self
    {
        $this->no_site = $no_site;

        return $this;
    }

    /**
     * @return Collection|Inscription[]
     */
    public function getNoInscription(): Collection
    {
        return $this->no_inscription;
    }

    public function addNoInscription(Inscription $noInscription): self
    {
        if (!$this->no_inscription->contains($noInscription)) {
            $this->no_inscription[] = $noInscription;
            $noInscription->setNoSortie($this);
        }

        return $this;
    }

    public function removeNoInscription(Inscription $noInscription): self
    {
        if ($this->no_inscription->contains($noInscription)) {
            $this->no_inscription->removeElement($noInscription);
            // set the owning side to null (unless already changed)
            if ($noInscription->getNoSortie() === $this) {
                $noInscription->setNoSortie(null);
            }
        }

        return $this;
    }

}
