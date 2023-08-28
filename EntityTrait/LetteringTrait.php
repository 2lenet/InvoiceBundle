<?php

namespace Lle\InvoiceBundle\EntityTrait;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Lle\InvoiceBundle\Model\InvoiceInterface;
use Lle\InvoiceBundle\Model\PaymentInterface;
use Doctrine\ORM\Mapping as ORM;

trait LetteringTrait
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\OneToMany(targetEntity: InvoiceInterface::class, mappedBy: 'lettering', cascade: ['persist'])]
    private $invoices;

    #[ORM\OneToMany(targetEntity: PaymentInterface::class, mappedBy: 'lettering', cascade: ['persist'])]
    private $payments;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private \DateTime $date;

    public function __construct()
    {
        $this->invoices = new ArrayCollection();
        $this->payments = new ArrayCollection();
    }

    public function __toString(): string
    {
        return (string)$this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, InvoiceInterface>
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(InvoiceInterface $invoice): self
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices[] = $invoice;
            $invoice->setLettering($this);
        }

        return $this;
    }

    public function removeInvoice(InvoiceInterface $invoice): self
    {
        if ($this->invoices->removeElement($invoice)) {
            // set the owning side to null (unless already changed)
            if ($invoice->getLettering() === $this) {
                $invoice->setLettering(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PaymentInterface>
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(PaymentInterface $payment): self
    {
        if (!$this->payments->contains($payment)) {
            $this->payments[] = $payment;
            $payment->setLettering($this);
        }

        return $this;
    }

    public function removePayment(PaymentInterface $payment): self
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getLettering() === $this) {
                $payment->setLettering(null);
            }
        }

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getBalance(): float
    {
        $totalInvoiced = 0.0;
        $totalPaid = 0.0;
        /** @var PaymentInterface $payment */
        foreach ($this->payments as $payment) {
            $totalPaid += $payment->getAmountPaid();
        }
        /** @var InvoiceInterface $invoice */
        foreach ($this->invoices as $invoice){
            $totalInvoiced += $invoice->getTotalInclTax();
        }

        return $totalInvoiced - $totalPaid;
    }
}