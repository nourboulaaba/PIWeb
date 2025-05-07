<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
#[UniqueEntity(fields: ['email'], message: 'Un compte avec cet email existe déjà.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;
    #[ORM\Column(type: 'string', nullable: false)]
#[Assert\NotBlank(message: "L'email est requis.")]
#[Assert\Email(message: "L'adresse email est invalide.")]
private ?string $email = null;

#[ORM\Column(type: 'string', nullable: false)]
#[Assert\NotBlank(message: "Le mot de passe est requis.")]
#[Assert\Length(min: 6, minMessage: "Le mot de passe doit contenir au moins {{ limit }} caractères.")]
private ?string $password = null;


    #[ORM\Column(type: 'string', nullable: true)]
    //#[Assert\NotBlank(message: "Le CIN est obligatoire.")]
    //#[Assert\Length(min: 8, max: 8, exactMessage: "Le CIN doit contenir exactement 8 chiffres.")]
    private ?string $cin = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $cv = null;

   // #[ORM\Column(type: 'string', nullable: true)]
    //#[Assert\NotBlank(message: "L'email est requis.")]
    //#[Assert\Email(message: "L'adresse email '{{ value }}' n'est pas valide.")]
    //private ?string $email = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $face_id = null;

    #[ORM\Column(type: 'string', nullable: true)]
    //#[Assert\NotBlank(message: "Le prénom est obligatoire.")]
    //#[Assert\Length(min: 2, max: 50, minMessage: "Le prénom est trop court.")]
    private ?string $first_name = null;

    #[ORM\Column(type: 'string', nullable: true)]
    //#[Assert\NotBlank(message: "La date d'embauche est requise.")]
    private ?string $hire_date = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $identifier = null;

    #[ORM\Column(type: 'string', nullable: true)]
    //#[Assert\NotBlank(message: "Le nom est obligatoire.")]
    private ?string $last_name = null;

    //#[ORM\Column(type: 'string', nullable: true)]
    //#[Assert\NotBlank(message: "Le mot de passe est requis.")]
    //#[Assert\Length(min: 8, minMessage: "Le mot de passe doit contenir au moins 8 caractères.")]
   // private ?string $password = null;

    #[ORM\Column(type: 'string', nullable: true)]
    //#[Assert\NotBlank(message: "Le numéro de téléphone est requis.")]
    //#[Assert\Regex(pattern: "/^[0-9]{8}$/", message: "Le numéro de téléphone doit contenir exactement 8 chiffres.")]
    private ?string $phone_number = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $profile_photo = null;

    #[ORM\Column(type: 'string', nullable: true)]
    //#[Assert\NotBlank(message: "Le rôle est obligatoire.")]
    private ?string $role = null;

    #[ORM\Column(type: 'decimal', nullable: false)]
    //#[Assert\NotBlank(message: "Le salaire est requis.")]
    //#[Assert\Positive(message: "Le salaire doit être un nombre positif.")]
    private ?float $salary = null;

    #[ORM\Column(name: "is_verified", type: "boolean", nullable: true)]
    private ?bool $isVerified = null;
   
    // Getters & Setters
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getCin(): ?string
    {
        return $this->cin;
    }

    public function setCin(?string $cin): self
    {
        $this->cin = $cin;
        return $this;
    }

    public function getCv(): ?string
    {
        return $this->cv;
    }

    public function setCv(?string $cv): self
    {
        $this->cv = $cv;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getFaceId(): ?string
    {
        return $this->face_id;
    }

    public function setFaceId(?string $face_id): static
    {
        $this->face_id = $face_id;
        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(?string $first_name): static
    {
        $this->first_name = $first_name;
        return $this;
    }

    public function getHireDate(): ?string
    {
        return $this->hire_date;
    }

    public function setHireDate(?string $hire_date): static
    {
        $this->hire_date = $hire_date;
        return $this;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(?string $identifier): self
    {
        $this->identifier = $identifier;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(?string $last_name): static
    {
        $this->last_name = $last_name;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phone_number;
    }

    public function setPhoneNumber(?string $phone_number): static
    {
        $this->phone_number = $phone_number;
        return $this;
    }

    public function getProfilePhoto(): ?string
    {
        return $this->profile_photo;
    }

    public function setProfilePhoto(?string $profile_photo): static
    {
        $this->profile_photo = $profile_photo;
        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function getRoles(): array
{
    $role = strtoupper($this->role);

    return ['ROLE_' . $role];
}

    public function getSalary(): ?float
    {
        return $this->salary;
    }

    public function setSalary(float $salary): self
    {
        $this->salary = $salary;
        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified ?? false;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;
        return $this;
    }

    public function eraseCredentials()
    {
        // Méthode requise par UserInterface mais non utilisée ici
    }

    public function getUserIdentifier(): string
    {
        return (string)$this->email; // Symfony recommande d’utiliser l’email comme identifiant
    }
}

