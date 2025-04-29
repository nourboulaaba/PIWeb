<?php

namespace App\Entity;

use App\Repository\CongeRepository;
use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CongeRepository::class)]
#[ORM\Table(name: 'conges')]
class Conge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'conges')]
    #[ORM\JoinColumn(name: 'idEmploye', referencedColumnName: 'id', nullable: false)]
    private ?User $employe = null;

    #[ORM\Column(name: 'dateDebut', type: 'date')]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(name: 'dateFin', type: 'date')]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(name: 'typeConge', type: 'string', length: 200)]
    private ?string $typeConge = null;

    #[ORM\Column(name: 'statut', type: 'string', length: 100)]
    private ?string $statut = self::STATUT_EN_ATTENTE;

    const STATUT_EN_ATTENTE = 'en attente';
    const STATUT_APPRUVE = 'approuvé';
    const STATUT_REFUSE = 'refusé';

    public function getId(): ?int
    {
        return $this->id;
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
        if ($this->dateDebut && $this->dateFin) {
            return $this->dateDebut->diff($this->dateFin)->days + 1;
        }
        return 0;
    }
}
