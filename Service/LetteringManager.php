<?php

namespace Lle\InvoiceBundle\Service;


use App\Entity\Lettering;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Lle\InvoiceBundle\Model\CustomerInterface;
use Lle\InvoiceBundle\Model\InvoiceInterface;
use Lle\InvoiceBundle\Model\LetteringInterface;
use Lle\InvoiceBundle\Model\PaymentInterface;

Class LetteringManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }
    public function letter(iterable $invoices, iterable $payments): ?LetteringInterface
    {
        $invoices = $this->cleanLettredInvoices($invoices);
        $payments = $this->cleanLettredPayments($payments);

        if (!$invoices && !$payments) {
            return null;
        }

        $sumInvoice = $this->getInvoicesSum($invoices);
        $sumPayment = $this->getPaymentsSum($payments);
        $diff = $sumInvoice - $sumPayment;
        if (abs($diff) <= 0.01) {
            $lettering = $this->newLettering();
            /** @var InvoiceInterface $invoice */
            foreach ($invoices as $invoice) {
                $invoice->setLettering($lettering);
            }
            /** @var PaymentInterface $payment */
            foreach ($payments as $payment) {
                $payment->setLettering($lettering);
            }

            return $lettering;
        }

        return null;
    }

    public function getInvoicesSum(iterable $invoices): float
    {
        return array_reduce($invoices, fn($sumInvoice, $invoice) => /** @var InvoiceInterface $invoice */
$sumInvoice += (float)$invoice->getTotalInclTax(), 0.);
    }

    public function getPaymentsSum(iterable $payments): float
    {
        return array_reduce($payments, fn($sumPayment, $payment) => /** @var PaymentInterface $payment */
$sumPayment += $payment->getAmountPaid(), 0.);
    }

    public function newLettering(): LetteringInterface
    {
        $letteringClass = $this->entityManager->getClassMetadata(LetteringInterface::class)->getName();
        /** @var LetteringInterface $lettering */
        $lettering = new $letteringClass;
        $lettering->setDate(new \DateTime());

        return $lettering;
    }

    private function cleanLettredPayments(iterable $payments): array
    {
        $result = [];
        /** @var PaymentInterface $payment */
        foreach ($payments as $payment) {
            if ($payment->getLettering()) {
                continue;
            }
            $result[] = $payment;
        }

        return $result;
    }

    private function cleanLettredInvoices(iterable $invoices): array
    {
        $result = [];
        /** @var InvoiceInterface $invoice */
        foreach ($invoices as $invoice) {
            if ($invoice->getLettering() || $invoice->getStatus() === InvoiceInterface::STATUS_DRAFT) {
                continue;
            }
            $result[] = $invoice;
        }

        return $result;
    }
}