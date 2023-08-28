<?php


namespace Lle\InvoiceBundle\ExporterPaymentStrategy;


use Lle\InvoiceBundle\Model\PaymentInterface;

class PaymentExporterCSV implements PaymentExporterInterface
{


    public function process(array $payments, $bankAccount): string
    {
        return $this->export($payments, $bankAccount);
    }

    public function getFormat():string
    {
        return 'csv';
    }


    public function getName():string
    {
        return 'CSV';
    }

    public function export( $payments,$bankAccount ){
        $handle = tmpfile();
        /** @var PaymentInterface $payment */
        foreach ($payments as $payment){
            fputcsv($handle, $this->getLineCustomer($payment));
            fputcsv($handle, $this->getLineBank($payment,$bankAccount));
        }
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);
        return $content;
    }


    public function getLineCustomer(PaymentInterface $payment)
    {
        $line = [];
        $line[] = $payment->getCustomer()->getAccountingNumber();
        $line[] = "Clients";
        $line[] = "0";
        $line[] = $payment->getAmountPaid();
        return $line;
    }

    /**
     * @param $payment
     */
    public function getLineBank($payment,$bankAccount): array
    {
        $line = [];
        $line[] = $bankAccount;
        $line[] = "Banque";
        $line[] = $payment->getAmountPaid();
        $line[] = "0";
        return $line;
    }

}