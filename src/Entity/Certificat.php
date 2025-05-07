<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CertificatRepository;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CertificatRepository::class)]
#[ORM\Table(name: 'certificat')]
class Certificat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id_certif', type: 'integer')]
    private ?int $idCertif = null;

    public function getIdCertif(): ?int
    {
        return $this->idCertif;
    }

    public function setIdCertif(int $idCertif): self
    {
        $this->idCertif = $idCertif;
        return $this;
    }
    #[ORM\ManyToOne(inversedBy: 'certificats')] 
    #[ORM\JoinColumn(name: 'idFormation', referencedColumnName: 'id', nullable: true)]
    private ?Formation $formation = null;
    
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true)]
    private ?User $user = null;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }
    ##[ORM\ManyToOne(targetEntity: Formation::class, inversedBy: 'certificats')]
    ##[ORM\JoinColumn(name: 'idFormation', referencedColumnName: 'id')]
    ##[Assert\NotNull(message: "La formation associée est requise.")]
    #private ?Formation $formation = null;

    public function getFormation(): ?Formation
    {
        return $this->formation;
    }

    public function setFormation(?Formation $formation): self
    {
        $this->formation = $formation;
        return $this;
    }

    #[ORM\Column(name: 'date_examen', type: 'date')]
    #[Assert\NotBlank(message: "La date de l'examen est requise.")]
    private ?\DateTimeInterface $dateExamen = null;

    public function getDateExamen(): ?\DateTimeInterface
    {
        return $this->dateExamen;
    }

    public function setDateExamen(\DateTimeInterface $dateExamen): self
    {
        $this->dateExamen = $dateExamen;
        return $this;
    }

    #[ORM\Column(name: 'heure', type: 'time')]
    #[Assert\NotBlank(message: "L'heure de l'examen est requise.")]
    private ?\DateTimeInterface $heure = null;

    public function getHeure(): ?\DateTimeInterface
    {
        return $this->heure;
    }

    public function setHeure(?\DateTimeInterface $heure): self
    {
        $this->heure = $heure;
        return $this;
    }

    #[ORM\Column(name: 'duree', type: 'integer')]
    #[Assert\NotNull(message: "La durée est requise.")]
    #[Assert\Positive(message: "La durée doit être un entier positif.")]
    private ?int $duree = null;

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): self
    {
        $this->duree = $duree;
        return $this;
    }

    #[ORM\Column(name: 'prix_exam', type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Le prix de l'examen est requis.")]
    #[Assert\Regex(
        pattern: "/^\d+(\.\d{1,2})?$/",
        message: "Le prix doit être un nombre valide (ex: 100 ou 100.00)."
    )]
    private ?string $prixExam = null;

    public function getPrixExam(): ?string
    {
        return $this->prixExam;
    }

    public function setPrixExam(string $prixExam): self
    {
        $this->prixExam = $prixExam;
        return $this;
    }

    #[ORM\Column(name: 'niveau', type: 'string', length: 255)]
    #[Assert\NotBlank(message: "Le niveau est requis.")]
    private ?string $niveau = null;

    public function getNiveau(): ?string
    {
        return $this->niveau;
    }

    public function setNiveau(string $niveau): self
    {
        $this->niveau = $niveau;
        return $this;
    }

    #[ORM\Column(name: 'resultat_examen', type: 'string', nullable: true, length: 255)]
    private ?string $resultatExamen = null;

    public function getResultatExamen(): ?string
    {
        return $this->resultatExamen;
    }

    public function setResultatExamen(?string $resultatExamen): self
    {
        $this->resultatExamen = $resultatExamen;
        return $this;
    }

    #[ORM\Column(name: 'date_reprogrammation', type: 'date', nullable: true)]
    #[Assert\Expression(
        "this.getDateReprogrammation() === null or this.getDateReprogrammation() >= this.getDateExamen()",
        message: "La date de reprogrammation doit être postérieure à la date d'examen."
    )]
    private ?\DateTimeInterface $dateReprogrammation = null;

    public function getDateReprogrammation(): ?\DateTimeInterface
    {
        return $this->dateReprogrammation;
    }

    public function setDateReprogrammation(?\DateTimeInterface $dateReprogrammation): self
    {
        $this->dateReprogrammation = $dateReprogrammation;
        return $this;
    }
}



