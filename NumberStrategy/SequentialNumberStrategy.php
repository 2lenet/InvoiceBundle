<?php

namespace Lle\InvoiceBundle\NumberStrategy;

use Lle\InvoiceBundle\Model\CustomerInterface;
use Lle\InvoiceBundle\Model\InvoiceInterface;
use Lle\InvoiceBundle\Model\InvoiceRepositoryInterface;
use Lle\InvoiceBundle\Model\ProductInterface;

class SequentialNumberStrategy implements NumberStrategyInterface
{
    public function __construct(private readonly InvoiceRepositoryInterface $invoiceRepo)
    {
    }

    public function setNumber(InvoiceInterface $invoice): InvoiceInterface
    {
        $seq_number =  $this->invoiceRepo->getMaxInvoiceNumberQuery()
            ->getQuery()
            ->getSingleScalarResult();
        if ($seq_number) {
            $seq_number += 1;
        } else {
            $seq_number = 1;
        }
        $invoice->setSequenceNumber($seq_number);
        $invoice->setInvoiceNumber((string) $seq_number);
        return $invoice;
    }
}

