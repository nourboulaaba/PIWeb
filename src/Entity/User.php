<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
class User
{

    #[ORM\Id]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "string", length: 255)]
    private string $cin;

    #[ORM\Column(type: "string", length: 255)]
    private string $cv;

    #[ORM\Column(type: "string", length: 255)]
    private string $email;

    #[ORM\Column(type: "string", length: 255)]
    private string $face_id;

    #[ORM\Column(type: "string", length: 255)]
    private string $first_name;

    #[ORM\Column(type: "string", length: 255)]
    private string $hire_date;

    #[ORM\Column(type: "string", length: 255)]
    private string $identifier;

    #[ORM\Column(type: "string", length: 255)]
    private string $last_name;

    #[ORM\Column(type: "string", length: 255)]
    private string $password;

    #[ORM\Column(type: "string", length: 255)]
    private string $phone_number;

    #[ORM\Column(type: "string", length: 255)]
    private string $profile_photo;

    #[ORM\Column(type: "string", length: 255)]
    private string $role;

    #[ORM\Column(type: "float")]
    private float $salary;

    #[ORM\Column(type: "boolean")]
    private bool $is_verified;

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getCin()
    {
        return $this->cin;
    }

    public function setCin($value)
    {
        $this->cin = $value;
    }

    public function getCv()
    {
        return $this->cv;
    }

    public function setCv($value)
    {
        $this->cv = $value;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($value)
    {
        $this->email = $value;
    }

    public function getFace_id()
    {
        return $this->face_id;
    }

    public function setFace_id($value)
    {
        $this->face_id = $value;
    }

    public function getFirst_name()
    {
        return $this->first_name;
    }

    public function setFirst_name($value)
    {
        $this->first_name = $value;
    }

    public function getHire_date()
    {
        return $this->hire_date;
    }

    public function setHire_date($value)
    {
        $this->hire_date = $value;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function setIdentifier($value)
    {
        $this->identifier = $value;
    }

    public function getLast_name()
    {
        return $this->last_name;
    }

    public function setLast_name($value)
    {
        $this->last_name = $value;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($value)
    {
        $this->password = $value;
    }

    public function getPhone_number()
    {
        return $this->phone_number;
    }

    public function setPhone_number($value)
    {
        $this->phone_number = $value;
    }

    public function getProfile_photo()
    {
        return $this->profile_photo;
    }

    public function setProfile_photo($value)
    {
        $this->profile_photo = $value;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setRole($value)
    {
        $this->role = $value;
    }

    public function getSalary()
    {
        return $this->salary;
    }

    public function setSalary($value)
    {
        $this->salary = $value;
    }

    public function getIs_verified()
    {
        return $this->is_verified;
    }

    public function setIs_verified($value)
    {
        $this->is_verified = $value;
    }
}
