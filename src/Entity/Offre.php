<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\OffreRepository;

#[ORM\Entity(repositoryClass: OffreRepository::class)]
#[ORM\Table(name: 'offre')]
class Offre
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

    #[ORM\Column(type: 'text', nullable: false)]
    private ?string $description = null;

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $salaireMin = null;

    public function getSalaireMin(): ?int
    {
        return $this->salaireMin;
    }

    public function setSalaireMin(int $salaireMin): self
    {
        $this->salaireMin = $salaireMin;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $salaireMax = null;

    public function getSalaireMax(): ?int
    {
        return $this->salaireMax;
    }

    public function setSalaireMax(int $salaireMax): self
    {
        $this->salaireMax = $salaireMax;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $departement_id = null;

    public function getDepartement_id(): ?int
    {
        return $this->departement_id;
    }

    public function setDepartement_id(int $departement_id): self
    {
        $this->departement_id = $departement_id;
        return $this;
    }

    public function getDepartementId(): ?int
    {
        return $this->departement_id;
    }

    public function setDepartementId(int $departement_id): static
    {
        $this->departement_id = $departement_id;

        return $this;
    }

}
