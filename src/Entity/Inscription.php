<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\InscriptionRepository")
 */
class Inscription
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_inscription;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Sortie", inversedBy="no_inscription")
     * @ORM\JoinColumn(nullable=false)
     */
    private $no_sortie;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="no_inscription")
     * @ORM\JoinColumn(nullable=false)
     */
    private $no_user;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateInscription(): ?\DateTimeInterface
    {
        return $this->date_inscription;
    }

    public function setDateInscription(\DateTimeInterface $date_inscription): self
    {
        $this->date_inscription = $date_inscription;

        return $this;
    }

    public function getNoSortie(): ?Sortie
    {
        return $this->no_sortie;
    }

    public function setNoSortie(?Sortie $no_sortie): self
    {
        $this->no_sortie = $no_sortie;

        return $this;
    }

    public function getNoUser(): ?User
    {
        return $this->no_user;
    }

    public function setNoUser(?User $no_user): self
    {
        $this->no_user = $no_user;

        return $this;
    }
}
