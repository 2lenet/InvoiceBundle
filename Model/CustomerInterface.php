<?php


namespace Lle\InvoiceBundle\Model;


use Doctrine\Common\Collections\Collection;

interface CustomerInterface
{
    public function getCompany();

    public function getName();

    public function getFirstName();

    public function getPhone(): ?string;

    public function getEmail(): ?string;

    public function setAccountingNumber(string $accountingNumber);

    public function getAccountingNumber(): ?string;

    public function getDiscount(): ?float;

    public function getBillingName(): ?string;

    public function getBillingEmail(): ?string;

    public function getIsDifferentBillingAddress(): ?bool;

    public function getBillingDepartment(): ?string;

    public function getBillingAddress1(): ?string;

    public function getBillingAddress2(): ?string;

    public function getBillingPostalCode(): ?string;

    public function getBillingCity(): ?string;

    public function getBillingCountry(): ?string;

    public function getVatNotApplicable(): ?bool;

    public function getPayments(): Collection;

    public function getInvoices(): Collection;
}
