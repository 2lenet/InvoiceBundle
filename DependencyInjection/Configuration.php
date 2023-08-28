<?php

namespace Lle\InvoiceBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Lle\InvoiceBundle\NumberStrategy\SequentialNumberStrategy;
use Lle\InvoiceBundle\ExporterStrategy\ExporterCSV;
use Lle\InvoiceBundle\ExporterPaymentStrategy\PaymentExporterCSV;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('lle_invoice');
        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
            ->scalarNode('invoice_number_strategy_service')->defaultValue(SequentialNumberStrategy::class)->end()
            ->scalarNode('invoice_exporter_strategy_service')->defaultValue(ExporterCSV::class)->end()
            ->scalarNode('payment_exporter_strategy_service')->defaultValue(PaymentExporterCSV::class)->end()
            ->scalarNode('invoice_repository')->defaultValue('App\\Repository\\InvoiceRepository')->end()
            ->scalarNode('logo_1')->defaultValue('')->end()
            ->scalarNode('header')->defaultValue('')->end()
            ->scalarNode('footer')->defaultValue('')->end()
            ->scalarNode('banque')->defaultValue('')->end()
            ->scalarNode('RIB')->defaultValue('')->end()
            ->scalarNode('IBAN')->defaultValue('')->end()
            ->scalarNode('BIC')->defaultValue('')->end()
            ->scalarNode('banque2')->defaultValue('')->end()
            ->scalarNode('RIB2')->defaultValue('')->end()
            ->scalarNode('IBAN2')->defaultValue('')->end()
            ->scalarNode('BIC2')->defaultValue('')->end()
            ->scalarNode('background')->defaultValue('')->end()
            ->scalarNode('bank_compta')->defaultValue('')->end()
        ;
        return $treeBuilder;
    }
}
