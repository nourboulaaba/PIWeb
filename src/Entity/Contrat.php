<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use App\Repository\ContratRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\TypeContratEnum;

#[ORM\Entity(repositoryClass: ContratRepository::class)]
#[ORM\Table(name: "contrat")]
class Contrat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "IdContrat", type: "integer")]
    private ?int $idContrat = null;

    #[ORM\Column(name: "IdEmploye", type: "integer")]
    private ?int $idEmploye = null;

    #[ORM\Column(name: "Type", type: "string", length: 50)]
    private ?string $type = null;

    #[ORM\Column(name: "DateDébut", type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(name: "DateFin", type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(name: "Salaire", type: "float")]
    private ?float $salaire = null;

    public function getIdContrat(): ?int
    {
        return $this->idContrat;
    }

    public function getIdEmploye(): ?int
    {
        return $this->idEmploye;
    }

    public function setIdEmploye(int $idEmploye): self
    {
        $this->idEmploye = $idEmploye;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
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

    public function getSalaire(): ?float
    {
        return $this->salaire;
    }

    public function setSalaire(float $salaire): self
    {
        $this->salaire = $salaire;
        return $this;
    }

    


    
}