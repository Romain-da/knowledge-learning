<?php

namespace App\Entity;

use App\Repository\CursusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CursusRepository::class)]
class Cursus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?float $prix = null;

    #[ORM\Column(length: 100)]
    private ?string $categorie = null;

    /**
     * @var Collection<int, Lecon>
     */
    #[ORM\OneToMany(targetEntity: Lecon::class, mappedBy: 'cursus', cascade: ['persist', 'remove'])]
    private Collection $lecons;

    /**
     * @var Collection<int, Achat>
     */
    #[ORM\OneToMany(targetEntity: Achat::class, mappedBy: 'cursus', cascade: ['persist', 'remove'])]
    private Collection $achats;

    /**
     * @var Collection<int, Certification>
     */
    #[ORM\OneToMany(targetEntity: Certification::class, mappedBy: 'cursus', cascade: ['persist', 'remove'])]
    private Collection $certifications;

    public function __construct()
    {
        $this->lecons = new ArrayCollection();
        $this->achats = new ArrayCollection();
        $this->certifications = new ArrayCollection();
        $this->prix = 0.0;
    }

    public function getId(): ?int { return $this->id; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }
    public function getPrix(): ?float { return $this->prix; }
    public function setPrix(float $prix): static { $this->prix = $prix; return $this; }
    public function getCategorie(): ?string { return $this->categorie; }
    public function setCategorie(string $categorie): static { $this->categorie = $categorie; return $this; }

    public function hasLecons(): bool
    {
        return !$this->lecons->isEmpty();
    }

    public function getLecons(): Collection { return $this->lecons; }
    public function addLecon(Lecon $lecon): static {
        if (!$this->lecons->contains($lecon)) {
            $this->lecons->add($lecon);
            $lecon->setCursus($this);
        }
        return $this;
    }
    public function removeLecon(Lecon $lecon): static {
        if ($this->lecons->removeElement($lecon) && $lecon->getCursus() === $this) {
            $lecon->setCursus(null);
        }
        return $this;
    }

    public function getAchats(): Collection { return $this->achats; }
    public function addAchat(Achat $achat): static {
        if (!$this->achats->contains($achat)) {
            $this->achats->add($achat);
            $achat->setCursus($this);
        }
        return $this;
    }
    public function removeAchat(Achat $achat): static {
        if ($this->achats->removeElement($achat) && $achat->getCursus() === $this) {
            $achat->setCursus(null);
        }
        return $this;
    }

    public function getCertifications(): Collection { return $this->certifications; }
    public function addCertification(Certification $certification): static {
        if (!$this->certifications->contains($certification)) {
            $this->certifications->add($certification);
            $certification->setCursus($this);
        }
        return $this;
    }
    public function removeCertification(Certification $certification): static {
        if ($this->certifications->removeElement($certification) && $certification->getCursus() === $this) {
            $certification->setCursus(null);
        }
        return $this;
    }
}
