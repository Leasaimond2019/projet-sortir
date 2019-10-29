<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 */
class SearchSortie
{
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Site")
     */
    private $site;

    private $nom;

    private $date_debut;

    private $date_fin;

    private $chk_organisateur;

    private $chk_inscrit;

    private $chk_non_inscrit;

    private $chk_passe;

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

    public function setDateDebut(\DateTimeInterface $date_debut = null): self
    {
        $this->date_debut = $date_debut;
        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin = null): self
    {
        $this->date_fin = $date_fin;
        return $this;
    }

    public function getSite()
    {
        return $this->site;
    }

    public function setSite($site): void
    {
        $this->site = $site;
    }

    public function getChkOrganisateur(): ?bool
    {
        return $this->chk_organisateur;
    }

    public function setChkOrganisateur(bool $chk_organisateur): self
    {
        $this->chk_organisateur = $chk_organisateur;

        return $this;
    }

    public function getChkInscrit(): ?bool
    {
        return $this->chk_inscrit;
    }

    public function setChkInscrit(bool $chk_inscrit): self
    {
        $this->chk_inscrit = $chk_inscrit;

        return $this;
    }

    public function getChkNonInscrit(): ?bool
    {
        return $this->chk_non_inscrit;
    }

    public function setChkNonInscrit(bool $chk_non_inscrit): self
    {
        $this->chk_non_inscrit = $chk_non_inscrit;

        return $this;
    }

    public function getChkPasse(): ?bool
    {
        return $this->chk_passe;
    }

    public function setChkPasse(bool $chk_passe): self
    {
        $this->chk_passe = $chk_passe;

        return $this;
    }
}
