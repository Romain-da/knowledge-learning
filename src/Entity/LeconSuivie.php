<?php

namespace App\Entity;

use App\Repository\LeconSuivieRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use App\Entity\Lecon;

#[ORM\Entity(repositoryClass: LeconSuivieRepository::class)]
class LeconSuivie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Lecon::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Lecon $lecon = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $dateVue;

    public function __construct()
    {
        $this->dateVue = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getLecon(): ?Lecon
    {
        return $this->lecon;
    }

    public function setLecon(?Lecon $lecon): self
    {
        $this->lecon = $lecon;
        return $this;
    }

    public function getDateVue(): \DateTimeInterface
    {
        return $this->dateVue;
    }

    public function setDateVue(\DateTimeInterface $dateVue): self
    {
        $this->dateVue = $dateVue;
        return $this;
    }
}
