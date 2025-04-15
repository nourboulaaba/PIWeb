<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

use App\Repository\MissionRepository;

#[ORM\Entity(repositoryClass: MissionRepository::class)]
#[ORM\Table(name: 'mission')]
class Mission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'idMission', type: 'integer')]
    private ?int $idMission = null;

    public function getIdMission(): ?int
    {
        return $this->idMission;
    }

    public function setIdMission(int $idMission): self
    {
        $this->idMission = $idMission;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]

    #[Assert\NotBlank(message: "Le titre est obligatoire.")]
    #[Assert\Length(min: 3, max: 100, minMessage: "Le titre doit contenir au moins {{ limit }} caractères.")]
    private ?string $titre = null;
    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: false)]
    #[Assert\NotNull(message: "La date est requise.")]
    #[Assert\Type("\DateTimeInterface", message: "Format de date invalide.")]
    #[Assert\GreaterThanOrEqual("today", message: "La date ne peut pas être dans le passé.")]
    private ?\DateTimeInterface $date = null;

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    #[Assert\NotBlank(message: "La destination est obligatoire.")]
    #[Assert\Length(min: 2, max: 100, minMessage: "La destination doit contenir au moins {{ limit }} caractères.")]
    private ?string $destination = null;

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setDestination(string $destination): self
    {
        $this->destination = $destination;
        return $this;
    }

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'missions')]
    #[ORM\JoinColumn(name: 'idEmploye', referencedColumnName: 'id')]
    #[Assert\NotNull(message: "Veuillez choisir un utilisateur.")]

    private ?Utilisateur $utilisateur = null;

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

}
