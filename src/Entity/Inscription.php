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
     * @ORM\Column(type="integer")
     */
    private $no_sortie;

    /**
     * @ORM\Column(type="integer")
     */
    private $no_participant;

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

    public function getNoSortie(): ?int
    {
        return $this->no_sortie;
    }

    public function setNoSortie(int $no_sortie): self
    {
        $this->no_sortie = $no_sortie;

        return $this;
    }

    public function getNoParticipant(): ?int
    {
        return $this->no_participant;
    }

    public function setNoParticipant(int $no_participant): self
    {
        $this->no_participant = $no_participant;

        return $this;
    }
}
