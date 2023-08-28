<?php


namespace Lle\InvoiceBundle\ExporterPaymentStrategy;



use Lle\InvoiceBundle\Model\PaymentInterface;

class PaymentExporterEBP implements PaymentExporterInterface
{


    public function process(array $payments,$bankAccount): string
    {
        return $this->export($payments,$bankAccount);
    }

    public function getFormat():string
    {
        return 'txt';
    }


    public function getName():string
    {
        return 'EBP';
    }



    public function export($payments,$bankAccount)
    {
        $handle = tmpfile();
        /** @var PaymentInterface $payment */
        foreach ($payments as $payment) {
            $line = $this->formatCSV($payment, $bankAccount, $payment->getAmountPaid(), 'D');
            fputcsv($handle, $line);
            $line = $this->formatCSV($payment, $payment->getCustomer()->getAccountingNumber(), $payment->getAmountPaid(), 'C');
            fputcsv($handle, $line);
        }
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return $content;
    }



    public function formatCSV(PaymentInterface $payment, $account, $amount, $type = 'C')
    {
        $paymentDate = $payment->getPaymentDate();
        $dueDate = $paymentDate;
        $customer = $payment->getCustomer();
        $invoices = $payment->getLettering()->getInvoices();
        $invoicesNumber = "";

        foreach ($invoices as $key => $invoice) {
            if ($key != 0) {
                $invoicesNumber .= ", ";
            }
            $invoicesNumber .= $invoice->getInvoiceNumber();
        }

        $typePayment = 'Regl Fact';
        if($payment->getPaymentType()){
            $typeP = $payment->getPaymentType() ;
            if($typeP->getCode() && $typeP->getCode() !== '') {
                $typePayment = $typeP->getCode();
            } else {
                $typePayment = $typeP->getLabel();
            }
        }

        $line = [];

        $line[] = 1;
        $line[] = $paymentDate->format('dmy');   // date payment
        $line[] = 'CI1';                            //code_journal
        $line[] = $account;                           //compte_compta
        $line[] = '';                           //compte_compta
        $line[] = $typePayment . ': ' . $invoicesNumber. ' '. $customer->getCompany();    //libelle facture
        $line[] = $invoicesNumber;    //num facture

        $line[] = $amount;
        $line[] = $type;
        $line[] = $dueDate->format('dmy');       //date_echeance
        $line[] = 'E';

        return $line;
    }

}