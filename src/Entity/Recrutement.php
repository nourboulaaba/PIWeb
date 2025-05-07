<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\RecrutementRepository;

#[ORM\Entity(repositoryClass: RecrutementRepository::class)]
#[ORM\Table(name: 'recrutement')]
class Recrutement
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

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $offre_id = null;

    public function getOffre_id(): ?int
    {
        return $this->offre_id;
    }

    public function setOffre_id(int $offre_id): self
    {
        $this->offre_id = $offre_id;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: false)]
    private ?\DateTimeInterface $dateDebut = null;

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): self
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: true)]
    private ?\DateTimeInterface $dateFin = null;

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): self
    {
        $this->dateFin = $dateFin;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?int $NbEntretien = null;

    public function getNbEntretien(): ?int
    {
        return $this->NbEntretien;
    }

    public function setNbEntretien(int $NbEntretien): self
    {
        $this->NbEntretien = $NbEntretien;
        return $this;
    }

    public function getOffreId(): ?int
    {
        return $this->offre_id;
    }

    public function setOffreId(int $offre_id): static
    {
        $this->offre_id = $offre_id;

        return $this;
    }

}
