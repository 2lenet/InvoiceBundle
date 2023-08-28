<?php

namespace Lle\InvoiceBundle\EntityTrait;

use Doctrine\ORM\Mapping as ORM;

trait InfoTrait
{

    #[ORM\Column(type: 'string', length: 255)]
    private $company;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $firstName;

    #[ORM\Column(type: 'string', length: 255)]
    private $postalCode;

    #[ORM\Column(type: 'string', length: 255)]
    private $billingAddress1;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $billingAddress2;

    #[ORM\Column(type: 'string', length: 255)]
    private $billingPostalCode;

    #[ORM\Column(type: 'string', length: 255)]
    private $billingCity;

    #[ORM\Column(type: 'string', length: 255)]
    private $billingCountry;

    #[ORM\Column(type: 'string', length: 255)]
    private $phone;

    #[ORM\Column(type: 'string', length: 255)]
    private $email;

    #[ORM\Column(type: 'string', length: 255)]
    private $billingEmail;

    #[ORM\Column(type: 'string', length: 255)]
    private $billingName;

    #[ORM\Column(type: 'string', length: 255)]
    private $billingDepartment;

    #[ORM\Column(type: 'boolean')]
    private $isDifferentBillingAddress;

    public function getCompany()
    {
        return $this->company;
    }

    public function setCompany($company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getBillingName(): ?string
    {
        return $this->billingName;
    }

    public function setBillingName($name): self
    {
        $this->billingName = $name;

        return $this;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName($firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getBillingAddress1(): ?string
    {
        return $this->billingAddress1;
    }

    public function setBillingAddress1(string $address): self
    {
        $this->billingAddress1 = $address;

        return $this;
    }

    public function getBillingAddress2(): ?string
    {
        return $this->billingaddress2;
    }

    public function setBillingAddress2(?string $address2): self
    {
        $this->billingaddress2 = $address2;

        return $this;
    }

    public function getBillingPostalCode(): ?string
    {
        return $this->billingpostalCode;
    }

    public function setBillingPostalCode(string $postalCode): self
    {
        $this->billingpostalCode = $postalCode;

        return $this;
    }

    public function getBillingCity(): ?string
    {
        return $this->billingCity;
    }

    public function setBillingCity(string $city): self
    {
        $this->billingCity = $city;

        return $this;
    }

    public function getBillingCountry(): ?string
    {
        return $this->billingCountryCode;
    }

    public function setBillingCountry(string $countryCode): self
    {
        $this->billingCountryCode = $countryCode;

        return $this;
    }

    public function getBillingEmail(): ?string
    {
        return $this->billingEmail;
    }

    public function setBillingEmail($email): self
    {
        $this->billingEmail = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getBillingDepartment(): ?string
    {
        return $this->billingDepartment;
    }

    public function setBillingDepartment(string $billingDepartment): self
    {
        $this->billingDepartment = $billingDepartment;

        return $this;
    }

    public function getIsDifferentBillingAddress(): ?bool
    {
        return $this->isDifferentBillingAdress;
    }

    public function setIsDifferentBillingAddress(bool $isDifferentBillingAdress): self
    {
        $this->isDifferentBillingAdress = $isDifferentBillingAdress;

        return $this;
    }

}
