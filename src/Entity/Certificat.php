<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use App\Repository\CertificatRepository;

#[ORM\Entity(repositoryClass: CertificatRepository::class)]
#[ORM\Table(name: 'certificat')]
class Certificat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
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

    #[ORM\ManyToOne(targetEntity: Formation::class, inversedBy: 'certificats')]
    #[ORM\JoinColumn(name: 'idFormation', referencedColumnName: 'id')]
    private ?Formation $formation = null;

    public function getFormation(): ?Formation
    {
        return $this->formation;
    }

    public function setFormation(?Formation $formation): self
    {
        $this->formation = $formation;
        return $this;
    }

    #[ORM\Column(type: 'date', nullable: false)]
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

    #[ORM\Column(type: 'time', nullable: false)]
    private ?string $heure = null;

    public function getHeure(): ?string
    {
        return $this->heure;
    }

    public function setHeure(string $heure): self
    {
        $this->heure = $heure;
        return $this;
    }

    #[ORM\Column(type: 'integer', nullable: false)]
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

    #[ORM\Column(type: 'string', nullable: false)]
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

    #[ORM\Column(type: 'string', nullable: false)]
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

    #[ORM\Column(type: 'string', nullable: true)]
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

    #[ORM\Column(type: 'date', nullable: true)]
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
