<?php

namespace App\Entity;

use App\Repository\LeconRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

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
    #[ORM\JoinColumn(nullable: true)] // Correction: nullable pour éviter les erreurs SQL
    private ?Cursus $cursus = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'boolean')]
    private bool $isValidated = false;

    public function __construct()
    {
        $this->createdAt = new \DateTime(); // Auto-set la date à la création
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

    // ✅ Ajout d'une méthode __toString() pour l'affichage dans les formulaires
    public function __toString(): string
    {
        return $this->titre;
    }
}
