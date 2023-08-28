<?php

namespace Lle\InvoiceBundle\DependencyInjection;

use Lle\PdfGeneratorBundle\Generator\PdfGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class LleInvoiceExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('lle_invoice.logo_1', $config['logo_1']);
        $container->setParameter('lle_invoice.header', $config['header']);
        $container->setParameter('lle_invoice.footer', $config['footer']);
        $container->setParameter('lle_invoice.banque', $config['banque']);
        $container->setParameter('lle_invoice.RIB', $config['RIB']);
        $container->setParameter('lle_invoice.IBAN', $config['IBAN']);
        $container->setParameter('lle_invoice.BIC', $config['BIC']);
        $container->setParameter('lle_invoice.banque2', $config['banque2']);
        $container->setParameter('lle_invoice.RIB2', $config['RIB2']);
        $container->setParameter('lle_invoice.IBAN2', $config['IBAN2']);
        $container->setParameter('lle_invoice.BIC2', $config['BIC2']);
        $container->setParameter('lle_invoice.background', $config['background']);
        $container->setParameter('lle_invoice.bank_compta', $config['bank_compta']);
        $container->setParameter('lle_invoice.invoice_number_strategy_service', $config['invoice_number_strategy_service']);
        $container->setParameter('lle_invoice.invoice_exporter_strategy_service', $config['invoice_exporter_strategy_service']);
        $container->setParameter('lle_invoice.payment_exporter_strategy_service', $config['payment_exporter_strategy_service']);
        $container->setParameter('lle_invoice.invoice_repository', $config['invoice_repository']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
    }
}
