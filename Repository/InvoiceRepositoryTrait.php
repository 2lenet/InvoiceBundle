<?php

namespace Lle\InvoiceBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Lle\InvoiceBundle\Model\InvoiceInterface;
use Lle\InvoiceBundle\Model\InvoiceRepositoryInterface;

trait InvoiceRepositoryTrait
{
    public function getMaxInvoiceNumberQuery()
    {
        $qb = $this->createQueryBuilder("invoice");
        return $qb->select("MAX(invoice.sequenceNumber)");
    }

    public function getFilteredCollectionQb($filter_state)
    {
        $qb = $this->createQueryBuilder('invoice');
        $filter_state->applyFilters($qb, 'Invoice');

        return $qb;
    }

    public function getFilteredCollection($filter_state) {
        $qb = $this->getFilteredCollectionQb($filter_state);

        return $qb->getQuery()->getResult();
    }

    public function getOverdueInvoices($limitDate, $today)
    {
        $qb = $this->createQueryBuilder('entity')
            ->where('entity.isPaid = 0')
            ->andWhere('entity.dueDate >= :dateLimite')
            ->andWhere('entity.dueDate <= :today')
            ->setParameter('dateLimite', $limitDate)
            ->setParameter('today', $today)
        ;

        return $qb->getQuery()->execute();
    }

    public function getInvoicesForCSV($filter_state)
    {
        $qb = $this->getFilteredCollectionQb($filter_state);
        $qb->andWhere('invoice.isExportedAccounting = false');

        return $qb->getQuery()->execute();
    }

    public function getInvoicesForRelance($filter_state, $reminder)
    {
        $qb = $this->getFilteredCollectionQb($filter_state);
        $qb->andWhere('invoice.reminder = :reminder')
            ->setParameter('reminder', $reminder)
            ->andWhere("invoice.status NOT LIKE 'Draft'")
            ->andWhere("invoice.type NOT LIKE 'Avoir'")
        ;

        return $qb->getQuery()->execute();
    }
}
