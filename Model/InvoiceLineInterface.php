<?php

namespace Lle\InvoiceBundle\Model;

use Lle\InvoiceBundle\Model\InvoiceInterface;
use Lle\InvoiceBundle\Model\ProductInterface;
use Lle\InvoiceBundle\Model\VatRateInterface;

interface InvoiceLineInterface
{
    public function invoiceNotExportedAccounting();

    public function getLabel(): ?string;

    public function setLabel(string $label): self;

    public function getSpecificLabel(): ?string;

    public function setSpecificLabel(?string $specificLabel): self;

    public function getQuantity(): ?string;

    public function getQuantityAsInt(): ?string;

    public function setQuantity(string $quantity): self;

    public function getCode(): ?string;

    public function setCode(string $code): self;

    public function getUnitPrice(): ?string;

    public function getRealUnitPrice(): ?string;

    public function setUnitPrice(string $unitPrice): self;

    public function getDiscount(): ?string;

    public function setDiscount(?string $discount): self;

    public function getTotalLineExclTax(): ?string;

    public function setTotalLineExclTax(string $totalLineExclTax): self;

    public function getTotalLineInclTax(): ?string;

    public function setTotalLineInclTax(string $totalLineInclTax): self;

    public function getVatRate(): ?string;

    public function setVatRate(string $vatRate): self;

    public function getProduct(): ?ProductInterface;

    public function setProduct(?ProductInterface $product): self;

    public function getInvoice(): ?InvoiceInterface;

    public function setInvoice(?InvoiceInterface $invoice): self;

    public function getVat(): ?VatRateInterface;

    public function setVat(?VatRateInterface $vatRate): self;

    public function getComment();

    public function setComment($comment);

    public function getStartDate(): ?\DateTimeInterface;

    public function setStartDate(?\DateTimeInterface $startDate): self;

    public function getEndDate(): ?\DateTimeInterface;

    public function setEndDate(?\DateTimeInterface $endDate): self;

    public function getIsManual(): ?bool;

    public function setIsManual(?bool $isManual): self;

    public function updateTotalInvoiceLine(): InvoiceLineInterface;

    public function calcTotalLineExclTax();

    public function calcTotalLineInclTax();

    public function getTotalDiscountLine();

    public function initProduct(ProductInterface $product): self;
}