<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\FormationRepository;

#[ORM\Entity(repositoryClass: FormationRepository::class)]
#[ORM\Table(name: 'formations')]
class Formation
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

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $name = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $description = null;

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    #[ORM\Column(type: 'string', nullable: false)]
    private ?string $prix = null;

    public function getPrix(): ?string
    {
        return $this->prix;
    }

    public function setPrix(string $prix): self
    {
        $this->prix = $prix;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Certificat::class, mappedBy: 'formation')]
    private Collection $certificats;

    public function __construct()
    {
        $this->certificats = new ArrayCollection();
    }

    /**
     * @return Collection<int, Certificat>
     */
    public function getCertificats(): Collection
    {
        if (!$this->certificats instanceof Collection) {
            $this->certificats = new ArrayCollection();
        }
        return $this->certificats;
    }

    public function addCertificat(Certificat $certificat): self
    {
        if (!$this->getCertificats()->contains($certificat)) {
            $this->getCertificats()->add($certificat);
            $certificat->setFormation($this);
        }
        return $this;
    }

    public function removeCertificat(Certificat $certificat): self
    {
        if ($this->getCertificats()->removeElement($certificat)) {
            // set the owning side to null (unless already changed)
            if ($certificat->getFormation() === $this) {
                $certificat->setFormation(null);
            }
        }
        return $this;
    }
}