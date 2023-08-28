<?php


namespace Lle\InvoiceBundle\Service;

use Lle\InvoiceBundle\Model\InvoiceInterface;
use Lle\InvoiceBundle\Model\InvoiceLineInterface;
use Twig\Environment;

class ZugFerdExporter
{
    public function __construct(private readonly Environment $twig)
    {
    }

    public function export(InvoiceInterface $invoice): string
    {
        return $this->twig->render('@LleInvoice/export/zugferd.xml.twig', ["invoice" => $invoice, "data" => $this->getData($invoice)]);
    }

    protected function getData(InvoiceInterface $invoice): array
    {
        $data = ['lines' => [], 'taxes' => [], 'total' => ['total_excl_tax' => 0, 'total_incl_tax' => 0, 'total_taxes' => 0]];

        /** @var InvoiceLineInterface $invoiceLine */
        foreach ($invoice->getInvoiceLines() as $invoiceLine) {
            $line = [
                'label' => $invoiceLine->getLabel(),
                'unit_price_excl_tax' => $invoiceLine->getRealUnitPrice(),
                'quantity' => $invoiceLine->getQuantity(),
                'vat_rate' => $invoiceLine->getVatRate(),
                'total_excl_tax' => $invoiceLine->getTotalLineExclTax()
            ];


            if (!isset($data['taxes'][$invoiceLine->getVatRate()])) {
                $data['taxes'][$invoiceLine->getVatRate()] = [
                    'tax_rate' => $invoiceLine->getVatRate(),
                    'total_excl_tax' => 0,
                    'total_tax' => 0
                ];
            }
            $data['taxes'][$invoiceLine->getVatRate()]['total_tax'] += (float)$invoiceLine->getTotalLineInclTax() - (float)$invoiceLine->getTotalLineExclTax();
            $data['taxes'][$invoiceLine->getVatRate()]['total_excl_tax'] += $line['total_excl_tax'];
            $data['lines'][] = $line;
        }
        $totalPaid = 0;
        $payment_mode = "";
        $payment_information = "";
        if ($invoice->getLettering()) {
            foreach ($invoice->getLettering()->getPayments() as $payment) {
                $totalPaid+= $payment->getAmountPaid();
                if ($payment->getPaymentType()) {
                    $payment_mode .= $payment->getPaymentType()->getZugferdCode() . " ";
                    $payment_information .= $payment->getPaymentType()->getLabel() . " ";
                }
            }
        }
        $data['invoice_type'] = $invoice->getType() == InvoiceInterface::TYPE_CREDIT ? '381' : '380'; //381 Avoir - 380 facture
        $data['payment_information'] = $payment_information;

        $data['total']['total_paid'] = $totalPaid;
        $data['total']['total_due'] = $invoice->getBalance();
        $data['total']['total_taxes'] = $invoice->getTotalTax();
        $data['total']['total_incl_tax'] = $invoice->getTotalInclTax();
        $data['total']['total_excl_tax'] = $invoice->getTotalExclTax();

        return $data;
    }
}
