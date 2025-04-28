<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Departement;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity]
class Offre
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]  // Ensure that the ID is auto-generated

    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "The title cannot be empty.")]
    #[Assert\Regex(
        pattern: "/^[^0-9]*$/",
        message: "The title cannot contain numbers."
    )]
    private string $titre;

    #[ORM\Column(type: "text")]
    #[Assert\NotBlank(message: "The description cannot be empty.")]

    private string $description;

    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank(message: "The minimum salary cannot be empty.")]
    #[Assert\GreaterThan(
        value: 0,
        message: "The minimum salary must be greater than 0."
    )]
    private int $salaireMin;

    #[ORM\Column(type: "integer")]
    #[Assert\NotBlank(message: "The maximum salary cannot be empty.")]
    #[Assert\GreaterThan(
        propertyPath: "salaireMin",
        message: "The maximum salary must be greater than the minimum salary."
    )]
    private int $salaireMax;

    #[ORM\ManyToOne(targetEntity: Departement::class, inversedBy: "offres")]
    #[ORM\JoinColumn(name: 'departement_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[Assert\NotNull(message: "The department cannot be null.")]

    private Departement $departement;

    public function getId(): int
    {
        return $this->id;
    }



    public function getTitre(): string
    {
        return $this->titre;
    }

    public function setTitre(string $value): void
    {
        $this->titre = $value;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $value): void
    {
        $this->description = $value;
    }

    public function getSalaireMin(): int
    {
        return $this->salaireMin;
    }

    public function setSalaireMin(int $value): void
    {
        $this->salaireMin = $value;
    }

    public function getSalaireMax(): int
    {
        return $this->salaireMax;
    }

    public function setSalaireMax(int $value): void
    {
        $this->salaireMax = $value;
    }

    public function getDepartement(): Departement
    {
        return $this->departement;
    }

    public function setDepartement(Departement $value): void
    {
        $this->departement = $value;
    }
}
