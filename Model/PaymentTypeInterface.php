<?php


namespace Lle\InvoiceBundle\Model;


interface PaymentTypeInterface
{
    public const PAYMENT_STATUS_SUCCEEDED = 'succeeded';
    public const PAYMENT_STATUS_PROCESSING = 'processing';
    public const PAYMENT_STATUS_CAPTURED = 'captured';
    public const PAYMENT_STATUS_FAILED = 'failed';

    public function __toString(): string;

    public function getLabel(): ?string;

    public function getCode(): ?string;
}