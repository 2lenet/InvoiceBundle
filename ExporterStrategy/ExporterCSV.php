<?php


namespace Lle\InvoiceBundle\ExporterStrategy;


use Lle\InvoiceBundle\Model\InvoiceInterface;
use Lle\InvoiceBundle\Model\InvoiceLineInterface;

class ExporterCSV implements ExporterInterface
{


    public function process(array $invoices): string
    {
        return $this->export($invoices);
    }

    public function getFormat():string
    {
        return 'csv';
    }


    public function getName():string
    {
        return 'CSV';
    }

    public function export( $invoices ){
        $handle = tmpfile();
        /** @var InvoiceInterface $invoice */
        foreach ($invoices as $invoice){
            /** @var InvoiceLineInterface $invoiceLine */
            foreach ($invoice->getInvoiceLines() as $invoiceLine){
                fputcsv($handle, $this->getLineVat($invoiceLine));
                fputcsv($handle, $this->getLineExclTax($invoiceLine));
                fputcsv($handle, $this->getLineInclTax($invoice, $invoiceLine));
            }
        }
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);
        return $content;
    }

    private function getLineVat(InvoiceLineInterface $invoiceLine): array
    {
        $line = [];
        $line[] = $invoiceLine->getProduct()->getVat()->getAccountingNumber();
        $line[] = 'TVA collectée';
        $line[] = 0;
        $line[] = (float)$invoiceLine->getQuantity() * (float)$invoiceLine->getProduct()->getUnitPrice() *
            (float)$invoiceLine->getProduct()->getVat()->getRate();
        return $line;
    }

    private function getLineExclTax(InvoiceLineInterface $invoiceLine): array
    {
        $line = [];
        $line[] = $invoiceLine->getProduct()->getAccountingNumber();
        $line[] = $invoiceLine->getProduct()->getLabel();
        $line[] = 0;
        $line[] = (float)$invoiceLine->getQuantity() * (float)$invoiceLine->getProduct()->getUnitPrice();

        return $line;
    }

    private function getLineInclTax(InvoiceInterface $invoice, InvoiceLineInterface $invoiceLine): array
    {
        $line = [];
        $line[] = $invoice->getCustomer()->getAccountingNumber();
        $line[] = $invoice->getCustomer()->getName();
        $line[] = (float)$invoiceLine->getQuantity() * (float)$invoiceLine->getProduct()->getUnitPrice(
            ) * (1 + (float)$invoiceLine->getProduct()->getVat()->getRate());
        $line[] = 0;

        return $line;
    }
}