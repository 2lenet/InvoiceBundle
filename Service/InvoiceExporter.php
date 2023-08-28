<?php


namespace Lle\InvoiceBundle\Service;

use Lle\InvoiceBundle\ExporterStrategy\ExporterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class InvoiceExporter
{
    public function __construct(private readonly ExporterInterface $exporter)
    {
    }

    public function export(array $invoices)
    {
        return $this->exporter->process($invoices);
    }
}