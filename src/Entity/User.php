<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\UserRepository;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user')]
class User
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

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $cin = null;

    public function getCin(): ?string
    {
        return $this->cin;
    }

    public function setCin(?string $cin): static
    {
        $this->cin = $cin;

        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $cv = null;

    public function getCv(): ?string
    {
        return $this->cv;
    }

    public function setCv(?string $cv): static
    {
        $this->cv = $cv;

        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $email = null;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $face_id = null;

    public function getFace_id(): ?string
    {
        return $this->face_id;
    }

    public function setFace_id(?string $face_id): self
    {
        $this->face_id = $face_id;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $first_name = null;

    public function getFirst_name(): ?string
    {
        return $this->first_name;
    }

    public function setFirst_name(?string $first_name): self
    {
        $this->first_name = $first_name;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $hire_date = null;

    public function getHire_date(): ?string
    {
        return $this->hire_date;
    }

    public function setHire_date(?string $hire_date): self
    {
        $this->hire_date = $hire_date;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $identifier = null;

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(?string $identifier): static
    {
        $this->identifier = $identifier;

        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $last_name = null;

    public function getLast_name(): ?string
    {
        return $this->last_name;
    }

    public function setLast_name(?string $last_name): self
    {
        $this->last_name = $last_name;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $password = null;

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;

        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $phone_number = null;

    public function getPhone_number(): ?string
    {
        return $this->phone_number;
    }

    public function setPhone_number(?string $phone_number): self
    {
        $this->phone_number = $phone_number;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $profile_photo = null;

    public function getProfile_photo(): ?string
    {
        return $this->profile_photo;
    }

    public function setProfile_photo(?string $profile_photo): self
    {
        $this->profile_photo = $profile_photo;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $role = null;

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): static
    {
        $this->role = $role;

        return $this;
    }

    #[ORM\Column(type: 'decimal', nullable: false)]
    private ?float $salary = null;

    public function getSalary(): ?string
    {
        return $this->salary;
    }

    public function setSalary(string $salary): static
    {
        $this->salary = $salary;

        return $this;
    }

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $is_verified = null;

    public function is_verified(): ?bool
    {
        return $this->is_verified;
    }

    public function setIs_verified(?bool $is_verified): self
    {
        $this->is_verified = $is_verified;
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

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(?string $last_name): static
    {
        $this->last_name = $last_name;

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

    public function isVerified(): ?bool
    {
        return $this->is_verified;
    }

    public function setIsVerified(?bool $is_verified): static
    {
        $this->is_verified = $is_verified;

        return $this;
    }

}
