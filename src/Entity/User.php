<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Un compte existe déjà avec cet email.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: "json")]
    private array $roles = [];

    #[ORM\Column]
    private bool $isVerified = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $prenom = null;

    #[ORM\OneToMany(targetEntity: Achat::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $achats;

    #[ORM\OneToMany(targetEntity: Certification::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $certifications;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: AchatLecon::class, orphanRemoval: true)]
    private Collection $achatLecons;

    public function __construct()
    {
        $this->achats = new ArrayCollection();
        $this->certifications = new ArrayCollection();
        $this->achatLecons = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }

    public function getEmail(): ?string { return $this->email; }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string { return $this->password; }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        if (!in_array('ROLE_USER', $roles, true)) {
            $roles[] = 'ROLE_USER';
        }
        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        if (!in_array('ROLE_USER', $roles, true)) {
            $roles[] = 'ROLE_USER';
        }
        $this->roles = array_unique($roles);
        return $this;
    }

    public function isVerified(): bool { return $this->isVerified; }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getNom(): ?string { return $this->nom; }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string { return $this->prenom; }

    public function setPrenom(?string $prenom): static
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function eraseCredentials(): void {}

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @return Collection<int, Achat>
     */
    public function getAchats(): Collection { return $this->achats; }

    public function addAchat(Achat $achat): static
    {
        if (!$this->achats->contains($achat)) {
            $this->achats->add($achat);
            $achat->setUser($this);
        }
        return $this;
    }

    public function removeAchat(Achat $achat): static
    {
        if ($this->achats->removeElement($achat) && $achat->getUser() === $this) {
            $achat->setUser(null);
        }
        return $this;
    }

    /**
     * @return Collection<int, Certification>
     */
    public function getCertifications(): Collection { return $this->certifications; }

    public function addCertification(Certification $certification): static
    {
        if (!$this->certifications->contains($certification)) {
            $this->certifications->add($certification);
            $certification->setUser($this);
        }
        return $this;
    }

    public function removeCertification(Certification $certification): static
    {
        if ($this->certifications->removeElement($certification) && $certification->getUser() === $this) {
            $certification->setUser(null);
        }
        return $this;
    }

    /**
     * @return Collection<int, AchatLecon>
     */
    public function getAchatLecons(): Collection
    {
        return $this->achatLecons;
    }

    public function addAchatLecon(AchatLecon $achatLecon): static
    {
        if (!$this->achatLecons->contains($achatLecon)) {
            $this->achatLecons[] = $achatLecon;
            $achatLecon->setUser($this);
        }

        return $this;
    }

    public function removeAchatLecon(AchatLecon $achatLecon): static
    {
        if ($this->achatLecons->removeElement($achatLecon) && $achatLecon->getUser() === $this) {
            $achatLecon->setUser(null);
        }

        return $this;
    }
}
