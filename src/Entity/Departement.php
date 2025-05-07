<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Offre;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Departement
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]  // Ensure that the ID is auto-generated

    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "The department name cannot be empty.")]
    #[Assert\Regex(
        pattern: "/^[^0-9]*$/",
        message: "The department name cannot contain numbers."
    )]
    private string $nom;



    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "responsable_id", referencedColumnName: "id", nullable: false)]
    private ?User $responsable = null;

    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank(message: "The budget cannot be empty.")]
    #[Assert\GreaterThan(
        value: 0,
        message: "The budget must be greater than 0."
    )]
    private int $budget;

    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank(message: "The number of employees cannot be empty.")]
    #[Assert\GreaterThanOrEqual(
        value: 0,
        message: "The number of employees cannot be negative."
    )]
    private int $nbEmploye;

    #[ORM\OneToMany(mappedBy: "departement", targetEntity: Offre::class)]
    private Collection $offres;

    public function __construct()
    {
        $this->offres = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }



    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $value): void
    {
        $this->nom = $value;
    }



    public function getBudget(): int
    {
        return $this->budget;
    }

    public function setBudget(int $value): void
    {
        $this->budget = $value;
    }

    public function getNbEmploye(): int
    {
        return $this->nbEmploye;
    }

    public function setNbEmploye(int $value): void
    {
        $this->nbEmploye = $value;
    }

    public function getOffres(): Collection
    {
        return $this->offres;
    }

    public function addOffre(Offre $offre): self
    {
        if (!$this->offres->contains($offre)) {
            $this->offres[] = $offre;
            $offre->setDepartement($this);
        }

        return $this;
    }

    public function removeOffre(Offre $offre): self
    {
        if ($this->offres->removeElement($offre)) {
            if ($offre->getDepartement() === $this) {
                $offre->setDepartement(null);
            }
        }

        return $this;
    }

    public function getResponsable(): ?User
    {
        return $this->responsable;
    }

    public function setResponsable(?User $responsable): void
    {
        $this->responsable = $responsable;
    }

    public function __toString(): string
    {
        return $this->nom; // ou toute autre propriété descriptive
    }

}
