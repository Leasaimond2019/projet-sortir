<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $pseudo;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $mail;

    /**
     * @ORM\Column(type="boolean")
     */
    private $actif;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sortie", mappedBy="no_organisateur",cascade={"remove"})
     */
    private $sorties;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Site", inversedBy="users" )
     * @ORM\JoinColumn(nullable=false)
     */
    private $no_site;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Inscription", mappedBy="no_user",cascade={"remove"})
     */
    private $no_inscription;

    public function __construct()
    {
        $this->sorties = new ArrayCollection();
        $this->no_inscription = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->pseudo;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getSorties(): Collection
    {
        return $this->sorties;
    }

    public function addSorty(Sortie $sorty): self
    {
        if (!$this->sorties->contains($sorty)) {
            $this->sorties[] = $sorty;
            $sorty->setNoOrganisateur($this);
        }

        return $this;
    }

    public function removeSorty(Sortie $sorty): self
    {
        if ($this->sorties->contains($sorty)) {
            $this->sorties->removeElement($sorty);
            // set the owning side to null (unless already changed)
            if ($sorty->getNoOrganisateur() === $this) {
                $sorty->setNoOrganisateur(null);
            }
        }

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
            $noInscription->setNoUser($this);
        }

        return $this;
    }

    public function removeNoInscription(Inscription $noInscription): self
    {
        if ($this->no_inscription->contains($noInscription)) {
            $this->no_inscription->removeElement($noInscription);
            // set the owning side to null (unless already changed)
            if ($noInscription->getNoUser() === $this) {
                $noInscription->setNoUser(null);
            }
        }

        return $this;
    }
}
