<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Application
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank(message: "Please upload your CV.")]
    private string $cvPath;

    #[ORM\Column(type: "float")]
    private float $atsScore;

    #[ORM\ManyToOne(targetEntity: Recrutement::class)]  // Changed from Entretien to Recrutement
    #[ORM\JoinColumn(nullable: false)]
    private Recrutement $recrutement;  // Changed from Entretien to Recrutement

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    public function getId(): int
    {
        return $this->id;
    }

    public function getCvPath(): string
    {
        return $this->cvPath;
    }

    public function setCvPath(string $cvPath): void
    {
        $this->cvPath = $cvPath;
    }

    public function getAtsScore(): float
    {
        return $this->atsScore;
    }

    public function setAtsScore(float $atsScore): void
    {
        $this->atsScore = $atsScore;
    }

    public function getRecrutement(): Recrutement  // Changed from getEntretien to getRecrutement
    {
        return $this->recrutement;  // Changed from entretien to recrutement
    }

    public function setRecrutement(Recrutement $recrutement): void  // Changed from entretien to recrutement
    {
        $this->recrutement = $recrutement;  // Changed from entretien to recrutement
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}
