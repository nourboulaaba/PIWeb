<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\MissionRepository;

#[ORM\Entity(repositoryClass: MissionRepository::class)]
#[ORM\Table(name: 'mission')]
class Mission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_mission', type: 'integer')]
    private ?int $idMission = null;

    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    #[Assert\NotBlank(message: "Le titre est obligatoire.")]
    #[Assert\Length(
        min: 3, 
        max: 100,
        minMessage: "Le titre doit contenir au moins {{ limit }} caractères.",
        maxMessage: "Le titre ne peut pas dépasser {{ limit }} caractères."
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z0-9éèêëàâäôöûüçÉÈÊËÀÂÄÔÖÛÜÇ\s\-_,.!?]+$/",
        message: "Le titre contient des caractères non autorisés."
    )]
    private ?string $titre = null;

    #[ORM\Column(type: 'date', nullable: false)]
    #[Assert\NotNull(message: "La date est requise.")]
    #[Assert\Type("\DateTimeInterface", message: "La date doit être au format valide.")]
    #[Assert\GreaterThanOrEqual(
        "today", 
        message: "La date de mission ne peut pas être antérieure à aujourd'hui."
    )]
    #[Assert\LessThanOrEqual(
        "+1 year", 
        message: "La mission ne peut pas être planifiée plus d'un an à l'avance."
    )]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: 'string', length: 100, nullable: false)]
    #[Assert\NotBlank(message: "La destination est obligatoire.")]
    #[Assert\Length(
        min: 2,
        max: 100,
        minMessage: "La destination doit contenir au moins {{ limit }} caractères.",
        maxMessage: "La destination ne peut pas dépasser {{ limit }} caractères."
    )]
    #[Assert\Regex(
        pattern: "/^[a-zA-Z\s\-']+$/",
        message: "Seules les lettres, espaces et traits d'union sont autorisés."
    )]
    private ?string $destination = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'idEmploye', referencedColumnName: 'id', nullable: false)]
    #[Assert\NotNull(message: "Vous devez attribuer la mission à un employé.")]
    #[Assert\Valid]
    private ?User $user = null;

    #[ORM\OneToOne(targetEntity: Contrat::class)]
    #[ORM\JoinColumn(
        name: "IdContrat",           // Nom de la colonne **dans la table mission**
        referencedColumnName: "id_contrat",  // Nom de la colonne dans la table contrat
        nullable: true
    )]
    private ?Contrat $contrat = null;



    // Getters et Setters

    public function getIdMission(): ?int
    {
        return $this->idMission
        
        ;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setDestination(string $destination): self
    {
        $this->destination = $destination;
        return $this;
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

    public function getContrat(): ?Contrat
    {
        return $this->contrat;
    }

    public function setContrat(?Contrat $contrat): static
    {
        $this->contrat = $contrat;

        return $this;
    }
}
