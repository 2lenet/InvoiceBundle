<?php

namespace Lle\InvoiceBundle\Repository;

use Lle\InvoiceBundle\Model\InvoiceInterface;
use Lle\InvoiceBundle\Model\ProductInterface;

trait InvoiceLineRepositoryTrait
{
    public function getAllLinesOfInvoiceQueryBuilder(InvoiceInterface $invoice = null)
    {
        $qb = $this->createQueryBuilder('il')
            ->andWhere('il.invoice = :invoice')
            ->setParameter('invoice', $invoice);

        return $qb;
    }

    public function getAllInvoiceLineForProduct(ProductInterface $product)
    {
        $qb = $this->createQueryBuilder('il')
            ->innerJoin('il.invoice', 'i')
            ->andWhere('il.product = :product')
            ->setParameter('product', $product)
            ->andWhere('i.status LIKE :brouillonStatus')
            ->setParameter('brouillonStatus', InvoiceInterface::STATUS_DRAFT);

        return $qb->getQuery()->getResult();
    }

    public function findByInvoice($invoices = null)
    {
        $qb = $this->createQueryBuilder('il')
            ->select('il, invoice')
            ->join('il.invoice', 'invoice')
            ->where('invoice in (:invoice)')
            ->setParameter('invoice', $invoices);

        return $qb->getQuery()->getResult();
    }
}