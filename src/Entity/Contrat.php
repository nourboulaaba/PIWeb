<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\ContratRepository;

#[ORM\Entity(repositoryClass: ContratRepository::class)]
#[ORM\Table(name: 'contrat')]
class Contrat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $IdContrat = null;

    public function getIdContrat(): ?int
    {
        return $this->IdContrat;
    }

    public function setIdContrat(int $IdContrat): self
    {
        $this->IdContrat = $IdContrat;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $IdEmploye = null;

    public function getIdEmploye(): ?int
    {
        return $this->IdEmploye;
    }

    public function setIdEmploye(int $IdEmploye): self
    {
        $this->IdEmploye = $IdEmploye;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $Type = null;

    public function getType(): ?string
    {
        return $this->Type;
    }

    public function setType(string $Type): self
    {
        $this->Type = $Type;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: false)]
    private ?\DateTimeInterface $DateDébut = null;

    public function getDateDébut(): ?\DateTimeInterface
    {
        return $this->DateDébut;
    }

    public function setDateDébut(\DateTimeInterface $DateDébut): self
    {
        $this->DateDébut = $DateDébut;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: false)]
    private ?\DateTimeInterface $DateFin = null;

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->DateFin;
    }

    public function setDateFin(\DateTimeInterface $DateFin): self
    {
        $this->DateFin = $DateFin;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $Salaire = null;

    public function getSalaire(): ?string
    {
        return $this->Salaire;
    }

    public function setSalaire(string $Salaire): self
    {
        $this->Salaire = $Salaire;
        return $this;
    }

}
