<?php


namespace Lle\InvoiceBundle\Model;

use Lle\InvoiceBundle\Model\CustomerInterface;
use Lle\InvoiceBundle\Model\PaymentTypeInterface;


interface PaymentInterface
{
    public const PRE_PAID = 'pre-paid';
    public const PAID = 'paid';
    public const CANCEL = 'cancel';

    public function canDelete();

    public function canEdit();

    public function getAmountPaid();

    public function setAmountPaid($amountPaid): self;

    public function getChequeNumber();

    public function setChequeNumber($chequeNumber): self;

    public function getPaymentDate(): ?\DateTimeInterface;

    public function setPaymentDate(?\DateTimeInterface $paymentDate): self;

    public function getCustomer(): ?CustomerInterface;

    public function setCustomer(?CustomerInterface $customer): self;

    public function getPaymentType(): ?PaymentTypeInterface;

    public function setPaymentType(PaymentTypeInterface $paymentType): self;

    public function getIsExportedAccounting(): ?bool;

    public function setIsExportedAccounting(?bool $isExportedAccounting): self;

    public function getStatus(): ?string;

    public function setStatus(?string $status): self;

    public function getLettering(): ?LetteringInterface;

    public function setLettering(?LetteringInterface $lettering): self;

    public function isLettered(): bool;
}