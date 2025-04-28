<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
class Utilisateur
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 50)]
    private string $lastName;

    #[ORM\Column(type: "string", length: 50)]
    private string $firstName;

    #[ORM\Column(type: "string", length: 50)]
    private string $identifier;

    #[ORM\Column(type: "string", length: 255)]
    private string $email;

    #[ORM\Column(type: "string", length: 255)]
    private string $password;

    #[ORM\Column(type: "string", length: 200)]
    private string $CIN;

    #[ORM\Column(type: "string", length: 255)]
    private string $role;

    #[ORM\Column(type: "string", length: 200)]
    private string $faceId;

    #[ORM\Column(type: "string", length: 255)]
    private string $salary;

    #[ORM\Column(type: "string", length: 200)]
    private string $hireDate;

    #[ORM\Column(type: "string", length: 20)]
    private string $phoneNumber;

    #[ORM\Column(type: "string", length: 200)]
    private string $cv;

    #[ORM\Column(type: "string", length: 200)]
    private string $profilePhoto;

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName($value)
    {
        $this->lastName = $value;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName($value)
    {
        $this->firstName = $value;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function setIdentifier($value)
    {
        $this->identifier = $value;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($value)
    {
        $this->email = $value;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($value)
    {
        $this->password = $value;
    }

    public function getCIN()
    {
        return $this->CIN;
    }

    public function setCIN($value)
    {
        $this->CIN = $value;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setRole($value)
    {
        $this->role = $value;
    }

    public function getFaceId()
    {
        return $this->faceId;
    }

    public function setFaceId($value)
    {
        $this->faceId = $value;
    }

    public function getSalary()
    {
        return $this->salary;
    }

    public function setSalary($value)
    {
        $this->salary = $value;
    }

    public function getHireDate()
    {
        return $this->hireDate;
    }

    public function setHireDate($value)
    {
        $this->hireDate = $value;
    }

    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber($value)
    {
        $this->phoneNumber = $value;
    }

    public function getCv()
    {
        return $this->cv;
    }

    public function setCv($value)
    {
        $this->cv = $value;
    }

    public function getProfilePhoto()
    {
        return $this->profilePhoto;
    }

    public function setProfilePhoto($value)
    {
        $this->profilePhoto = $value;
    }
}
