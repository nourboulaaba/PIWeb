<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CongeRepository;
use App\Entity\User;

#[ORM\Entity(repositoryClass: CongeRepository::class)]
#[ORM\Table(name: 'conges')]
class Conge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'idConges', type: 'integer')]
    private ?int $idConges = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'idEmploye', referencedColumnName: 'id', nullable: false)]
    private ?User $employe = null;

    #[ORM\Column(name: 'dateDebut', type: 'date')]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(name: 'dateFin', type: 'date')]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(name: 'typeConge', type: 'string', length: 200)]
    private ?string $typeConge = null;

    #[ORM\Column(name: 'statut', type: 'string', length: 100)]
    private ?string $statut = 'en attente';

    // Constante pour les statuts possibles
    const STATUT_EN_ATTENTE = 'en attente';
    const STATUT_APPRUVE = 'approuvé';
    const STATUT_REFUSE = 'refusé';

    // Getters et setters
    public function getIdConges(): ?int
    {
        return $this->idConges;
    }

    public function getEmploye(): ?User
    {
        return $this->employe;
    }

    public function setEmploye(User $employe): self
    {
        $this->employe = $employe;
        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;
        return $this;
    }

    public function getTypeConge(): ?string
    {
        return $this->typeConge;
    }

    public function setTypeConge(string $typeConge): self
    {
        $this->typeConge = $typeConge;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;
        return $this;
    }

    public function getDuree(): int
    {
        // Vérification des dates avant calcul
        if ($this->dateDebut && $this->dateFin) {
            return $this->dateDebut->diff($this->dateFin)->days + 1;
        }
        return 0; // Retourne 0 si les dates sont nulles
    }
}