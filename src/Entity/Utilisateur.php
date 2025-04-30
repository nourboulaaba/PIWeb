<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\UtilisateurRepository;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\Table(name: 'utilisateur')]
class Utilisateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
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

    #[ORM\Column(name: 'lastName', type: 'string', length: 255, nullable: false)]
    private ?string $lastName = null;

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    #[ORM\Column(name: 'firstName', type: 'string', length: 255, nullable: false)]
    private ?string $firstName = null;

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    #[ORM\Column(name: 'identifier', type: 'string', length: 255, nullable: true)]
    private ?string $identifier = null;

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(?string $identifier): self
    {
        $this->identifier = $identifier;
        return $this;
    }

    #[ORM\Column(name: 'email', type: 'string', length: 255, nullable: false)]
    private ?string $email = null;

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    #[ORM\Column(name: 'password', type: 'string', length: 255, nullable: true)]
    private ?string $password = null;

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;
        return $this;
    }

    #[ORM\Column(name: 'CIN', type: 'string', length: 255, nullable: true)]
    private ?string $CIN = null;

    public function getCIN(): ?string
    {
        return $this->CIN;
    }

    public function setCIN(?string $CIN): self
    {
        $this->CIN = $CIN;
        return $this;
    }

    #[ORM\Column(name: 'role', type: 'string', length: 255, nullable: true)]
    private ?string $role = null;

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(?string $role): self
    {
        $this->role = $role;
        return $this;
    }

    #[ORM\Column(name: 'faceId', type: 'string', length: 255, nullable: true)]
    private ?string $faceId = null;

    public function getFaceId(): ?string
    {
        return $this->faceId;
    }

    public function setFaceId(?string $faceId): self
    {
        $this->faceId = $faceId;
        return $this;
    }

    #[ORM\Column(name: 'salary', type: 'string', length: 255, nullable: true)]
    private ?string $salary = null;

    public function getSalary(): ?string
    {
        return $this->salary;
    }

    public function setSalary(?string $salary): self
    {
        $this->salary = $salary;
        return $this;
    }

    #[ORM\Column(name: 'hireDate', type: 'string', length: 255, nullable: true)]
    private ?string $hireDate = null;

    public function getHireDate(): ?string
    {
        return $this->hireDate;
    }

    public function setHireDate(?string $hireDate): self
    {
        $this->hireDate = $hireDate;
        return $this;
    }

    #[ORM\Column(name: 'phoneNumber', type: 'string', length: 255, nullable: true)]
    private ?string $phoneNumber = null;

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    #[ORM\Column(name: 'cv', type: 'string', length: 255, nullable: true)]
    private ?string $cv = null;

    public function getCv(): ?string
    {
        return $this->cv;
    }

    public function setCv(?string $cv): self
    {
        $this->cv = $cv;
        return $this;
    }

    #[ORM\Column(name: 'profilePhoto', type: 'string', length: 255, nullable: true)]
    private ?string $profilePhoto = null;

    public function getProfilePhoto(): ?string
    {
        return $this->profilePhoto;
    }

    public function setProfilePhoto(?string $profilePhoto): self
    {
        $this->profilePhoto = $profilePhoto;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Mission::class, mappedBy: 'utilisateur')]
    private Collection $missions;

    public function __construct()
    {
        $this->missions = new ArrayCollection();
    }

    /**
     * @return Collection<int, Mission>
     */
    public function getMissions(): Collection
    {
        return $this->missions;
    }

    public function addMission(Mission $mission): self
    {
        if (!$this->missions->contains($mission)) {
            $this->missions->add($mission);
            $mission->setUtilisateur($this);
        }
        return $this;
    }

    public function removeMission(Mission $mission): self
    {
        if ($this->missions->removeElement($mission)) {
            // set the owning side to null (unless already changed)
            if ($mission->getUtilisateur() === $this) {
                $mission->setUtilisateur(null);
            }
        }
        return $this;
    }
}