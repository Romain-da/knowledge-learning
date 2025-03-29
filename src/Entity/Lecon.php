<?php

namespace App\Entity;

use App\Repository\LeconRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Entity\LeconSuivie;

#[ORM\Entity(repositoryClass: LeconRepository::class)]
class Lecon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $titre;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $contenu = null;

    #[ORM\ManyToOne(inversedBy: 'lecons')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Cursus $cursus = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'boolean')]
    private bool $isValidated = false;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private ?float $prix = 0.0;

    #[ORM\OneToMany(mappedBy: 'lecon', targetEntity: LeconSuivie::class, orphanRemoval: true)]
    private Collection $suivies;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->prix = 0.0;
        $this->suivies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;
        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(?string $contenu): static
    {
        $this->contenu = $contenu;
        return $this;
    }

    public function getCursus(): ?Cursus
    {
        return $this->cursus;
    }

    public function setCursus(?Cursus $cursus): static
    {
        $this->cursus = $cursus;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function isValidated(): bool
    {
        return $this->isValidated;
    }

    public function setValidated(bool $isValidated): static
    {
        $this->isValidated = $isValidated;
        return $this;
    }

    public function toggleValidation(): static
    {
        $this->isValidated = !$this->isValidated;
        return $this;
    }

    public function getPrix(): float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;
        return $this;
    }

    public function __toString(): string
    {
        return $this->titre;
    }

    /** ✅ Ajout de la relation avec LeconSuivie **/

    /**
     * @return Collection<int, LeconSuivie>
     */
    public function getSuivies(): Collection
    {
        return $this->suivies;
    }

    public function addSuivie(LeconSuivie $suivie): static
    {
        if (!$this->suivies->contains($suivie)) {
            $this->suivies[] = $suivie;
            $suivie->setLecon($this);
        }

        return $this;
    }

    public function removeSuivie(LeconSuivie $suivie): static
    {
        if ($this->suivies->removeElement($suivie)) {
            if ($suivie->getLecon() === $this) {
                $suivie->setLecon(null);
            }
        }

        return $this;
    }
}
