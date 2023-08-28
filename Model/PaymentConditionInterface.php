<?php


namespace Lle\InvoiceBundle\Model;


interface PaymentConditionInterface
{
    public function getLabel();

    public function getNumberOfDays();

    public function getEndMonth();
}