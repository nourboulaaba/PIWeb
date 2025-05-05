<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity]
class Recrutement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "integer")]
    private int $offre_id;

    #[ORM\Column(type: "date")]
    #[Assert\NotNull(message: "La date de début est requise.")]

    private \DateTimeInterface $dateDebut;

    #[ORM\Column(type: "date", nullable: true)]  // Make dateFin nullable
    #[Assert\GreaterThan(propertyPath: "dateDebut", message: "La date de fin doit être après la date de début.")]

    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(type: "integer")]
    #[Assert\Positive(message: "Le nombre d'entretiens doit être un entier positif.")]

    private int $NbEntretien;

    // One Recrutement belongs to one Offre
    // One Recrutement belongs to one Offre
    #[ORM\OneToOne(targetEntity: Offre::class, inversedBy: 'recrutement')]
    #[ORM\JoinColumn(nullable: false)]
    private Offre $offre;

    // One Recrutement can have many Entretien
    #[ORM\OneToMany(mappedBy: 'recrutement', targetEntity: Entretien::class)]
    private Collection $entretiens;

    public function __construct()
    {
        $this->entretiens = new ArrayCollection(); // Initialize with ArrayCollection
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId($value): void
    {
        $this->id = $value;
    }

    public function getoffreId(): int
    {
        return $this->offre_id;
    }

    public function setoffreId($value): void
    {
        $this->offre_id = $value;
    }

    public function getDateDebut(): \DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut($value): void
    {
        $this->dateDebut = $value;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $value): void
    {
        $this->dateFin = $value;
    }

    public function getNbEntretien(): int
    {
        return $this->NbEntretien;
    }

    public function setNbEntretien($value): void
    {
        $this->NbEntretien = $value;
    }

    public function getOffre(): Offre
    {
        return $this->offre;
    }

    public function setOffre(Offre $offre): void
    {
        $this->offre = $offre;
    }

    public function getEntretiens(): Collection
    {
        return $this->entretiens;
    }

    public function addEntretien(Entretien $entretien): self
    {
        if (!$this->entretiens->contains($entretien)) {
            $this->entretiens[] = $entretien;
            $entretien->setRecrutement($this); // Link the entretien to this recrutement
        }

        return $this;
    }

    public function removeEntretien(Entretien $entretien): self
    {
        if ($this->entretiens->removeElement($entretien)) {
            // Set the owning side to null (because it's a bidirectional relationship)
            if ($entretien->getRecrutement() === $this) {
                $entretien->setRecrutement(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return sprintf(
            "Recrutement  Offre: %s, Nb Entretien: %d",

            $this->offre->getTitre(), // Assuming Offre has a getTitre method

            $this->NbEntretien
        );
    }
}
