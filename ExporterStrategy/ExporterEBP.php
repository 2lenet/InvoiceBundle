<?php


namespace Lle\InvoiceBundle\ExporterStrategy;


use Lle\InvoiceBundle\Model\InvoiceInterface;
use Cocur\Slugify\Slugify;
use Lle\InvoiceBundle\Model\InvoiceLineInterface;

class ExporterEBP implements ExporterInterface
{


    public function process(array $invoices): string
    {
        return $this->export($invoices);
    }

    public function getFormat():string
    {
        return 'txt';
    }


    public function getName():string
    {
        return 'EBP';
    }



    public function export($invoices)
    {
        $handle = tmpfile();
        /** @var InvoiceInterface $invoice */
        foreach ($invoices as $invoice) {

            /** @var InvoiceLineInterface $invoiceLine */

            $totalPct = 0;
            $i = 0;
            $nbLines = \count($invoice->getInvoiceLines());
            foreach ($invoice->getInvoiceLines() as $invoiceLine) {
                fputcsv($handle, $this->getInvoiceLineData($invoice, $invoiceLine));

                $part = 0;
                if ($i == $nbLines - 1) {
                    $part = 100 - $totalPct;
                } else {
                    $part = round( $invoiceLine->getTotalLineExclTax() * 100 / $invoice->getTotalExclTax() , 2);
                    $totalPct += $part;
                }
                fputcsv($handle, $this->getInvoiceLineDataComplement($invoice, $invoiceLine, $part));
                $i++;
            }

            $export = $this->getInvoiceVATData($invoice);
            foreach ($export as $line) {
                fputcsv($handle, $line);
            }
            fputcsv($handle, $this->getInvoiceData($invoice));
        }
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return $content;
    }


    public function getInvoiceLineData(InvoiceInterface $invoice, InvoiceLineInterface $invoiceLine)
    {

        $account = $invoiceLine->getProduct()->getAccountingNumber();

        if($invoiceLine->getTotalLineExclTax() > 0){
            $line = $this->formatCSV($invoice, $account, $invoiceLine->getTotalLineExclTax(), 'C');
        } else {
            $line = $this->formatCSV($invoice, $account, - (float)$invoiceLine->getTotalLineExclTax(), 'D');
        }

        return $line;
    }

    public function getInvoiceLineDataComplement(InvoiceInterface $invoice, InvoiceLineInterface $invoiceLine, $pct)
    {
        $line = [];

        $line[] = '>'.$invoiceLine->getProduct()->getAnalyticCode();
        $line[] = number_format($pct, 2, '.', '');
        if($invoiceLine->getTotalLineExclTax() > 0){
            $line[] = $invoiceLine->getTotalLineExclTax();
        } else {
            $line[] = -(float)$invoiceLine->getTotalLineExclTax();
        }


        return $line;
    }


    public function getInvoiceVATData(InvoiceInterface $invoice)
    {
        $tva = [];
        /** @var InvoiceLineInterface $invoiceLine */
        foreach ($invoice->getInvoiceLines() as $invoiceLine) {
            $vat = $invoiceLine->getProduct()->getVat();
            if ($vat && $vat->getAccountingNumber()) {
                $account = $vat->getAccountingNumber();
            }
            else {
                $account = 'TVA';
            }
            if(!array_key_exists($account, $tva)){
                $tva[$account] = 0;
            }
            $total = (float)$invoiceLine->getTotalLineInclTax() - (float)$invoiceLine->getTotalLineExclTax();
            $tva[$account] += $total;

        }


        $export = [];
        $invoideDate = $invoice->getInvoiceDate();
        $dueDate = $invoice->getDueDate();
        foreach ($tva as $account => $value) {
            if($value != 0){
                if ($value > 0) {
                    $line = $this->formatCSV($invoice, $account, $value, 'C');
                } else {
                    $line = $this->formatCSV($invoice, $account, -$value, 'D');
                }

                $export[] = $line;
            }

        }
        return $export;
    }


    public function getInvoiceData(InvoiceInterface $invoice, $totalTtc = null) {
        if ($totalTtc === null) {
            $totalTtc = $invoice->getTotalInclTax();
        }

        $line = [];
        if ($totalTtc > 0) {
            $line = $this->formatCSV($invoice, $invoice->getCustomer()->getAccountingNumber(), $totalTtc, 'D');
        } else {
            $line = $this->formatCSV($invoice, $invoice->getCustomer()->getAccountingNumber(), -$totalTtc, 'C');
        }

        return $line;
    }

    public function formatCSV($invoice, $account, $amount, $type = 'C')
    {
        $invoideDate = $invoice->getInvoiceDate();
        $dueDate = $invoice->getDueDate();
        $customer = $invoice->getCustomer();
        
        $line = [];
        $slugify = new Slugify();
        $line[] = 1;
        $line[] = $invoideDate->format('dmy');   // date facture
        $line[] = 'VT';                            //code_journal
        $line[] = $account;                           //compte_compta
        $line[] = '';                           //compte_compta
        $line[] = $invoice->getInvoiceNumber().'-'.strtoupper($slugify->slugify($customer->getCompany()));    //libelle facture
        $line[] = $invoice->getInvoiceNumber();    //num facture

        $line[] = $amount;
        $line[] = $type;
        $line[] = $dueDate->format('dmy');       //date_echeance
        $line[] = 'E';

        return $line;
    }

}
