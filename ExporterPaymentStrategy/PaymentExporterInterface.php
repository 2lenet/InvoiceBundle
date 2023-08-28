<?php

namespace Lle\InvoiceBundle\ExporterPaymentStrategy;


interface PaymentExporterInterface
{
    public function process(array $payments, $bankAccount): string ;

    public function getFormat():string;
    
    public function getName():string;
}