<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use App\Repository\ContratRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
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
    #[Assert\NotBlank(message: "L'ID de l'employé est requis.")]
    #[Assert\Positive(message: "L'ID de l'employé doit être un nombre positif.")]
    private ?int $idEmploye = null;

    #[ORM\Column(name: "Type", type: "string", length: 50)]
    #[Assert\NotBlank(message: "Le type de contrat est obligatoire.")]
    #[Assert\Choice(
        choices: ["CDI", "CDD", "Stage", "Interim", "Freelance"],
        message: "Le type de contrat doit être parmi CDI, CDD, Stage, Interim ou Freelance."
    )]
    private ?string $type = null;

    #[ORM\Column(name: "DateDébut", type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "La date de début est requise.")]
    #[Assert\LessThanOrEqual(
        propertyPath: "dateFin",
        message: "La date de début doit être antérieure ou égale à la date de fin."
    )]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(name: "DateFin", type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "La date de fin est requise.")]
    #[Assert\GreaterThanOrEqual(
        propertyPath: "dateDebut",
        message: "La date de fin doit être postérieure ou égale à la date de début."
    )]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(name: "Salaire", type: "float")]
    #[Assert\NotBlank(message: "Le salaire est requis.")]
    #[Assert\Positive(message: "Le salaire doit être un nombre positif.")]
    #[Assert\Range(
        min: 1000,
        max: 100000,
        notInRangeMessage: "Le salaire doit être compris entre {{ min }} et {{ max }}."
    )]
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