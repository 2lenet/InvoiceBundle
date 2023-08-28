<?php

namespace Lle\InvoiceBundle\NumberStrategy;

use Lle\InvoiceBundle\Model\InvoiceInterface;

interface NumberStrategyInterface
{
    public function setNumber(InvoiceInterface $invoice): InvoiceInterface;
}