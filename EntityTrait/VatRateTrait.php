<?php

namespace Lle\InvoiceBundle\EntityTrait;

use Doctrine\ORM\Mapping as ORM;

trait VatRateTrait
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private $rate;

    #[ORM\Column(type: 'string', length: 255)]
    private $label;

    #[ORM\Column(type: 'string', length: 255)]
    private $accountingNumber;

    public function __toString(): string
    {
        return $this->getLabel() . " (". $this->getRate() . "%))";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRate(): ?string
    {
        return $this->rate;
    }

    public function setRate(string $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getAccountingNumber(): ?string
    {
        return $this->accountingNumber;
    }

    public function setAccountingNumber(string $accountingNumber): self
    {
        $this->accountingNumber = $accountingNumber;

        return $this;
    }
}
