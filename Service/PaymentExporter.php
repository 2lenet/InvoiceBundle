<?php

namespace Lle\InvoiceBundle\Service;

use Lle\InvoiceBundle\ExporterPaymentStrategy\PaymentExporterInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PaymentExporter
{
    public function __construct(
        private readonly PaymentExporterInterface $exporter,
        private readonly ParameterBagInterface $parameterBag
    ) {
    }
    
    public function export(array $payments)
    {
        return $this->exporter->process($payments, $this->parameterBag->get('lle_invoice.bank_compta'));
    }
}