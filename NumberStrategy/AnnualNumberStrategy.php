<?php


namespace Lle\InvoiceBundle\NumberStrategy;


use Lle\InvoiceBundle\Model\CustomerInterface;
use Lle\InvoiceBundle\Model\InvoiceInterface;
use Lle\InvoiceBundle\Model\ProductInterface;
use Lle\InvoiceBundle\Model\InvoiceRepositoryInterface;

class AnnualNumberStrategy implements NumberStrategyInterface
{
    public function __construct(private readonly InvoiceRepositoryInterface $invoiceRepo)
    {
    }

    public function setNumber(InvoiceInterface $invoice): InvoiceInterface
    {
        $seq_number =  $this->invoiceRepo->getMaxInvoiceNumberQuery()
            ->andWhere('YEAR(invoice.invoiceDate) = :year')
            ->setParameter('year', $invoice->getInvoiceDate()->format('Y'))
            ->getQuery()
            ->getSingleScalarResult();
        if ($seq_number) {
            $seq_number += 1;
        } else {
            $seq_number = 1;
        }
        $invoice->setSequenceNumber($seq_number);
        if($invoice->getInvoiceDate()->format('Y') < 2022) {
            $invoice->setInvoiceNumber($invoice->getInvoiceDate()->format('Y').'-'.(string) $seq_number);
        } else {
            $invoice->setInvoiceNumber($invoice->getInvoiceDate()->format('Y').'-'. sprintf("%04d",$seq_number));
        }
        return $invoice;
    }
}

