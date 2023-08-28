<?php

namespace Lle\InvoiceBundle\Repository;

trait PaymentRepositoryTrait
{
    public function getAllPaymentByCustomerQueryBuilder($customer)
    {
        $qb = $this->createQueryBuilder('p')
            ->join('p.invoice', 'i')
            ->andWhere('i.customer = :customer')
            ->setParameter('customer', $customer);

        return $qb;
    }
}