<?php


namespace Lle\InvoiceBundle\Model;


interface VatRateInterface
{
    public function getRate();

    public function getAccountingNumber(): ?string;
}