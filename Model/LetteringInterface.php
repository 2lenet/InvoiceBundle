<?php

namespace Lle\InvoiceBundle\Model;

use Doctrine\Common\Collections\Collection;

interface LetteringInterface
{
    public function getInvoices(): Collection;

    public function addInvoice(InvoiceInterface $invoice): self;

    public function removeInvoice(InvoiceInterface $invoice): self;

    public function getPayments(): Collection;

    public function addPayment(PaymentInterface $payment): self;

    public function removePayment(PaymentInterface $payment): self;

    public function getDate(): ?\DateTimeInterface;

    public function setDate(\DateTimeInterface $date): self;
}