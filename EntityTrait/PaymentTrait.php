<?php

namespace Lle\InvoiceBundle\EntityTrait;

use Lle\InvoiceBundle\Model\CustomerInterface;
use Lle\InvoiceBundle\Model\InvoiceInterface;
use Lle\InvoiceBundle\Model\LetteringInterface;
use Lle\InvoiceBundle\Model\PaymentTypeInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

trait PaymentTrait
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;
    
    #[ORM\Column(type: 'decimal', precision: 18, scale: 2)]
    private $amountPaid;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $chequeNumber;
    
    #[ORM\Column(type: 'date')]
    #[Assert\LessThanOrEqual('today')]
    private $paymentDate;

    #[ORM\ManyToOne(targetEntity: CustomerInterface::class, inversedBy: 'payments')]
    private $customer;

    #[ORM\ManyToOne(targetEntity: PaymentTypeInterface::class)]
    private $paymentType;

    #[ORM\Column(type: 'boolean')]
    private $isExportedAccounting = false;


    #[ORM\Column(type: 'string', nullable: true)]
    private $status;

    #[ORM\ManyToOne(targetEntity: LetteringInterface::class, inversedBy: 'payments', cascade: ['persist'])]
    private $lettering;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function canDelete()
    {
        if ($this->isExportedAccounting) {
            return false;
        }

        return true;
    }

    public function canEdit()
    {
        if ($this->isExportedAccounting) {
            return false;
        }

        return true;
    }

    public function getAmountPaid()
    {
        return $this->amountPaid;
    }

    public function setAmountPaid($amountPaid): self
    {
        $this->amountPaid = $amountPaid;
        return $this;
    }

    public function getChequeNumber()
    {
        return $this->chequeNumber;
    }

    public function setChequeNumber($chequeNumber): self
    {
        $this->chequeNumber = $chequeNumber;
        return $this;
    }

    public function getPaymentDate(): ?\DateTimeInterface
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(?\DateTimeInterface $paymentDate): self
    {
        $this->paymentDate = $paymentDate;
        return $this;
    }

    public function getCustomer(): ?CustomerInterface
    {
        return $this->customer;
    }

    public function setCustomer(?CustomerInterface $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getPaymentType(): ?PaymentTypeInterface
    {
        return $this->paymentType;
    }

    public function setPaymentType(PaymentTypeInterface $paymentType): self
    {
        $this->paymentType = $paymentType;

        return $this;
    }


    public function getIsExportedAccounting(): ?bool
    {
        return $this->isExportedAccounting;
    }

    public function setIsExportedAccounting(?bool $isExportedAccounting): self
    {
        $this->isExportedAccounting = $isExportedAccounting;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }


    public function getLettering(): ?LetteringInterface
    {
        return $this->lettering;
    }

    public function setLettering(?LetteringInterface $lettering): self
    {
        $this->lettering = $lettering;

        return $this;
    }

    public function isLettered(): bool
    {
        return $this->lettering !== null;
    }

    public function isPrePaid(): bool
    {
        return $this->isLettered();
    }
}
