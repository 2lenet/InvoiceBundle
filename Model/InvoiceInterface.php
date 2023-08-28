<?php


namespace Lle\InvoiceBundle\Model;


use DateTimeInterface;
use Doctrine\Common\Collections\Collection;
use Lle\InvoiceBundle\Model\CustomerInterface;
use Lle\InvoiceBundle\Model\SellerInterface;

interface InvoiceInterface
{
    public const STATUS_VALIDATE = "Valid";
    public const STATUS_DRAFT = "Draft";
    public const STATUS_SEND = "Send";
    public const STATUS_BALANCE = "Balance";

    public const TYPE_CREDIT = "Avoir";
    public const TYPE_INVOICE = "Facture";

    public const NO_REMINDER = "NoReminder";
    public const REMINDER1 = "Reminder1";
    public const REMINDER2 = "Reminder2";

    public function getInvoiceNumber(): ?string;

    public function setInvoiceNumber(?string $invoiceNumber): self;

    public function getInvoiceDate();

    public function setInvoiceDate(DateTimeInterface $invoiceDate): self;

    public function getStatus();

    public function setStatus(string $status): self;

    public function addInvoiceLine(InvoiceLineInterface $invoiceLine): self;

    public function getInvoiceLines(): Collection;

    public function getCustomer(): ?CustomerInterface;

    public function getTotalExclTax(): ?string;

    public function setTotalExclTax(string $totalExclTax): self;

    public function getTotalInclTax(): ?string;

    public function setTotalInclTax(string $totalInclTax): self;

    public function getSeller(): ?SellerInterface;

    public function getDueDate(): ?\DateTimeInterface;

    public function setSequenceNumber($sequenceNumber);

    public function hasLineWithDiscount(): bool;

    public function getId(): ?int;

    public function getBillingAddress(): string;

    public function getType();

    public function getComment(): ?string;

    public function getAmountAlreadyPaid();

    public function getPaymentCondition(): ?PaymentConditionInterface;

    public function calcDueDate(): ?\DateTime;

    public function getDiscount(): ?string;

    public function setDiscount(?string $discount): self;

    public function initCustomer(CustomerInterface $customer): InvoiceInterface;

    public function getAddress(): ?string;

    public function getAddress2(): ?string;

    public function getPostalCode(): ?string;

    public function getCity(): ?string;

    public function getCountryCode(): ?string;

    public function getPhone(): ?string;

    public function getEmail(): ?string;

    public function getName();

    public function getFirstName();

    public function getIsPaid(): ?bool;

    public function setIsPaid(?bool $isPaid): self;

    public function setPaymentDate(?\DateTimeInterface $paymentDate): self;

    public function getCreditNote(): ?self;

    public function setDueDate(?\DateTimeInterface $dueDate): self;

    public function getSequenceNumber();

    public function updateTotalInvoice(): InvoiceInterface;

    public function setType(?string $type): self;

    public function setName(?string $name): self;

    public function setIsExportedAccounting(?bool $isExportedAccounting): self;

    public function setCreditNote(InvoiceInterface $creditNote): self;

    public function setPaymentCondition(?PaymentConditionInterface $paymentCondition): self;

    public function setFirstName(?string $firstName): self;

    public function setSeller(?SellerInterface $seller): self;

    public function setCustomer(?CustomerInterface $customer): self;

    public function setComment(?string $comment): self;

    public function setAddress(?string $address): self;

    public function setAddress2(?string $address): self;

    public function setCity(?string $city): self;

    public function setCountryCode(?string $countryCode): self;

    public function setPhone(?string $phone): self;

    public function setPostalCode(?string $postalCode): self;

    public function setEmail(?string $email): self;

    public function getLettering(): ?LetteringInterface;

    public function setLettering(?LetteringInterface $lettering): self;

    public function getTotalTax(): ?string;

    public function getBalance();
}
