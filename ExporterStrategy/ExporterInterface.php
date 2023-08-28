<?php

namespace Lle\InvoiceBundle\ExporterStrategy;


interface ExporterInterface
{
    public function process(array $invoices): string ;

    public function getFormat():string;
    
    public function getName():string;
    
}