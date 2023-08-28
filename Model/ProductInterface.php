<?php


namespace Lle\InvoiceBundle\Model;

use Lle\InvoiceBundle\Model\PaymentConditionInterface;
use Lle\InvoiceBundle\Model\VatRateInterface;

interface ProductInterface
{
    public function getAccountingNumber(): ?string;

    public function getVat(): ?VatRateInterface;

    public function getUnitPrice(): ?float;

    public function getLabel(): ?string;

    public function getCode(): ?string;

    public function getPaymentCondition(): ?PaymentConditionInterface;

    public function getAnalyticCode();
}