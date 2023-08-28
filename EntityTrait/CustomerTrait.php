<?php

namespace Lle\InvoiceBundle\EntityTrait;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Lle\InvoiceBundle\Model\InvoiceInterface;
use Lle\InvoiceBundle\Model\InvoiceLineInterface;
use \Lle\InvoiceBundle\Model\PaymentConditionInterface;
use Lle\InvoiceBundle\Model\PaymentInterface;

trait CustomerTrait
{
    use InfoTrait;

    #[ORM\Column(type: 'string', length: 255)]
    private $accountingNumber;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private $discount;

    #[ORM\Column(type: 'string', length: 255)]
    private $siret;

    #[ORM\Column(type: 'string', length: 255)]
    private $VatNumber;

    #[ORM\Column(type: 'boolean')]
    private $VatNotApplicable;

    #[ORM\ManyToOne(targetEntity: \Lle\InvoiceBundle\Model\PaymentConditionInterface::class)]
    private $paymentCondition;

    #[ORM\OneToMany(targetEntity: \Lle\InvoiceBundle\Model\PaymentInterface::class, mappedBy: 'customer', cascade: ['remove', 'persist'])]
    private Collection $payments;

    #[ORM\OneToMany(targetEntity: \Lle\InvoiceBundle\Model\InvoiceInterface::class, mappedBy: 'customer', cascade: ['remove', 'persist'])]
    private Collection $invoices;
    
    public function __construct()
    {
        $this->invoices = new ArrayCollection();
        $this->payments = new ArrayCollection();
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

    public function getDiscount(): ?float
    {
        return $this->discount;
    }

    public function setDiscount(string $discount): self
    {
        $this->discount = $discount;

        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(string $siret): self
    {
        $this->siret = $siret;

        return $this;
    }

    public function getVatNumber(): ?string
    {
        return $this->VatNumber;
    }

    public function setVatNumber(string $VatNumber): self
    {
        $this->VatNumber = $VatNumber;

        return $this;
    }

    public function getVatNotApplicable(): ?bool
    {
        return $this->VatNotApplicable;
    }

    public function setVatNotApplicable(bool $VatNotApplicable): self
    {
        $this->VatNotApplicable = $VatNotApplicable;

        return $this;
    }

    public function getPaymentCondition(): ?PaymentConditionInterface
    {
        return $this->paymentCondition;
    }

    public function setPaymentCondition(?PaymentConditionInterface $paymentCondition): self
    {
        $this->paymentCondition = $paymentCondition;

        return $this;
    }

    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(PaymentInterface $payment): self
    {
        $this->payments->add($payment);
        $payment->setCustomer($this);
        return $this;
    }

    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(InvoiceInterface $invoice): self
    {
        $this->invoices->add($invoice);
        $invoice->setCustomer($this);
        return $this;
    }
}
