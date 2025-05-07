<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\DepartementRepository;

#[ORM\Entity(repositoryClass: DepartementRepository::class)]
#[ORM\Table(name: 'departement')]
class Departement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $nom = null;

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $Responsable_ID = null;

    public function getResponsable_ID(): ?int
    {
        return $this->Responsable_ID;
    }

    public function setResponsable_ID(int $Responsable_ID): self
    {
        $this->Responsable_ID = $Responsable_ID;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $Budget = null;

    public function getBudget(): ?int
    {
        return $this->Budget;
    }

    public function setBudget(int $Budget): self
    {
        $this->Budget = $Budget;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $NbEmploye = null;

    public function getNbEmploye(): ?int
    {
        return $this->NbEmploye;
    }

    public function setNbEmploye(int $NbEmploye): self
    {
        $this->NbEmploye = $NbEmploye;
        return $this;
    }

    public function getResponsableID(): ?int
    {
        return $this->Responsable_ID;
    }

    public function setResponsableID(int $Responsable_ID): static
    {
        $this->Responsable_ID = $Responsable_ID;

        return $this;
    }

}
