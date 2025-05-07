<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Entretien
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;


    #[ORM\Column(type: "date")]
    #[Assert\NotNull(message: "La date de l'entretien est requise.")]
    private \DateTimeInterface $date;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "Le lieu ne peut pas être vide.")]
    private string $lieu;

    #[ORM\Column(type: "float")]
    #[Assert\NotBlank(message: "Longitude is required.")]
    #[Assert\Type(type: "float", message: "Longitude must be a valid floating-point number.")]
    #[Assert\GreaterThan(value: -180, message: "Longitude must be greater than -180.")]
    #[Assert\LessThan(value: 180, message: "Longitude must be less than 180.")]
    private float $longitude;

    #[ORM\Column(type: "float")]
    #[Assert\NotBlank(message: "Latitude is required.")]
    #[Assert\Type(type: "float", message: "Latitude must be a valid floating-point number.")]
    #[Assert\GreaterThan(value: -90, message: "Latitude must be greater than -90.")]
    #[Assert\LessThan(value: 90, message: "Latitude must be less than 90.")]
    private float $latitude;

    #[ORM\Column(type: "integer")]
    private int $recrutementId;

    #[ORM\Column(type: "boolean")]
    private bool $approved;

    #[ORM\ManyToOne(targetEntity: Recrutement::class, inversedBy: 'entretiens')]
    #[ORM\JoinColumn(nullable: false)]
    private Recrutement $recrutement;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $value): void
    {
        $this->id = $value;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }


    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $value): void
    {
        $this->date = $value;
    }

    public function getLieu(): string
    {
        return $this->lieu;
    }

    public function setLieu(string $value): void
    {
        $this->lieu = $value;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function setLongitude(float $value): void
    {
        $this->longitude = $value;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function setLatitude(float $value): void
    {
        $this->latitude = $value;
    }

    public function getRecrutementId(): int
    {
        return $this->recrutementId;
    }

    public function setRecrutementId(int $value): void
    {
        $this->recrutementId = $value;
    }

    public function getApproved(): bool
    {
        return $this->approved;
    }

    public function setApproved(bool $value): void
    {
        $this->approved = $value;
    }

    public function getRecrutement(): Recrutement
    {
        return $this->recrutement;
    }

    public function setRecrutement(Recrutement $recrutement): void
    {
        $this->recrutement = $recrutement;
    }
}
