<?php

namespace App\Entity;

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
     *  @var Etat
     * @ORM\ManyToOne(targetEntity="App\Entity\Etat")
     * @ORM\Column(type="integer")
     */
    private $no_etat;

    /**
     * @var Lieu
     * @ORM\ManyToOne(targetEntity="App\Entity\Lieu")
     * @ORM\Column(type="integer")
     */
    private $no_lieu;

    /**
     * @var Participant
     * @ORM\ManyToOne(targetEntity="App\Entity\Participant")
     * @ORM\Column(type="integer")
     */
    private $no_organisateur;

    /**
     * @var Site
     * @ORM\ManyToOne(targetEntity="App\Entity\Site")
     * @ORM\Column(type="integer")
     */
    private $site;

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

    public function getNoEtat(): ?int
    {
        return $this->no_etat;
    }

    public function setNoEtat(int $no_etat): self
    {
        $this->no_etat = $no_etat;

        return $this;
    }

    public function getNoLieu(): ?int
    {
        return $this->no_lieu;
    }

    public function setNoLieu(int $no_lieu): self
    {
        $this->no_lieu = $no_lieu;

        return $this;
    }

    public function getNoOrganisateur(): ?int
    {
        return $this->no_organisateur;
    }

    public function setNoOrganisateur(int $no_organisateur): self
    {
        $this->no_organisateur = $no_organisateur;

        return $this;
    }

    public function getSite(): ?int
    {
        return $this->site;
    }

    public function setSite(int $site): self
    {
        $this->site = $site;

        return $this;
    }
}
