<?php

namespace Lle\InvoiceBundle\Model;

interface InvoiceRepositoryInterface
{
    public function getMaxInvoiceNumberQuery();

    public function getFilteredCollectionQb($filter_state);

    public function getFilteredCollection($filter_state);

    public function getOverdueInvoices($limitDate, $today);

    public function getInvoicesForCSV($filter_state);

    public function getInvoicesForRelance($filter_state, $reminder);
}
