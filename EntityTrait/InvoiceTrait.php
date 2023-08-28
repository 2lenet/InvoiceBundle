<?php

namespace Lle\InvoiceBundle\EntityTrait;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Lle\InvoiceBundle\Model\CustomerInterface;
use Doctrine\Common\Collections\Collection;
use Lle\InvoiceBundle\Model\InvoiceInterface;
use Lle\InvoiceBundle\Model\InvoiceLineInterface;
use Lle\InvoiceBundle\Model\LetteringInterface;
use Lle\InvoiceBundle\Model\PaymentConditionInterface;
use Lle\InvoiceBundle\Model\PaymentInterface;
use Lle\InvoiceBundle\Model\SellerInterface;
use Symfony\Component\Serializer\Annotation\Groups;


trait InvoiceTrait
{
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $idChorus;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $company;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $firstName;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $address;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $address2;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $postalCode;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $city;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $countryCode;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $phone;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $email;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $invoiceNumber;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $sequenceNumber;


    #[ORM\Column(type: 'date', nullable: true)]
    private $invoiceDate;

    #[ORM\Column(type: 'date', nullable: true)]
    private $dueDate;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private $discount;

    #[ORM\Column(type: 'boolean')]
    private $isExportedAccounting;

    #[ORM\Column(type: 'string', length: 255)]
    private $status;

    #[ORM\Column(type: 'boolean')]
    private $isPaid;

    #[ORM\Column(type: 'text', nullable: true)]
    private $comment;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 2)]
    private string $totalExclTax = "0";

    #[ORM\Column(type: 'decimal', precision: 18, scale: 2)]
    private string $totalInclTax = "0";

    #[ORM\ManyToOne(targetEntity: \Lle\InvoiceBundle\Model\CustomerInterface::class, inversedBy: 'invoices')]
    #[ORM\JoinColumn(nullable: false)]
    private $customer;

    #[ORM\ManyToOne(targetEntity: \Lle\InvoiceBundle\Model\SellerInterface::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $seller;

    #[ORM\ManyToOne(targetEntity: \Lle\InvoiceBundle\Model\PaymentConditionInterface::class)]
    #[ORM\JoinColumn(nullable: true)]
    private $paymentCondition;

    #[ORM\OneToOne(targetEntity: \Lle\InvoiceBundle\Model\InvoiceInterface::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private $creditNote;

    #[ORM\OneToMany(targetEntity: \Lle\InvoiceBundle\Model\InvoiceLineInterface::class, mappedBy: 'invoice', cascade: ['remove', 'persist'])]
    private $invoiceLines;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $type = InvoiceInterface::TYPE_INVOICE;
    
    #[ORM\Column(type: 'string', length: 255)]
    private $reminder = InvoiceInterface::NO_REMINDER;

    #[ORM\Column(type: 'date', nullable: true)]
    private $paymentDate;

    #[ORM\Column(type: 'date', nullable: true)]
    private $lastReminderDate;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id = null;

    #[ORM\ManyToOne(targetEntity: LetteringInterface::class, inversedBy: 'invoices', cascade: ['persist'])]
    private $lettering;

    public function __construct()
    {
        $this->invoiceLines = new ArrayCollection();
    }
    public function __toString(): string
    {
        return $this->getType() . ($this->isValid() ? ' #' . $this->getInvoiceNumber() : ' ' . $this->getStatus());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isNotValid()
    {
        return $this->getStatus() === InvoiceInterface::STATUS_DRAFT;
    }

    public function isValid()
    {
        return !$this->isNotValid();
    }

    public function isValidAndNotExported()
    {
        return $this->isValid() && !$this->isExportedAccounting;
    }

    public function canCreateCredit(): bool
    {
        return !$this->getCreditNote() && $this->getType() !== self::TYPE_CREDIT;
    }

    public function getTotalTaxAmount(): float
    {
        return (float)$this->getTotalInclTax() - (float)$this->getTotalExclTax();
    }

    public function getTotalDiscount()
    {
        $totalDiscount = 0;
        /** @var InvoiceLineInterface $invoiceLine */
        foreach ($this->getInvoiceLines() as $invoiceLine) {
            $totalDiscount += $invoiceLine->getTotalDiscountLine();
        }

        return $totalDiscount;
    }

    public function updateTotalInvoice(): InvoiceInterface
    {
        $this->setTotalExclTax(0);
        $this->setTotalInclTax(0);
        /** @var InvoiceLineInterface $line */
        foreach ($this->getInvoiceLines() as $line) {
            $this->setTotalExclTax($this->getTotalExclTax() + $line->getTotalLineExclTax());
            $this->setTotalInclTax($this->getTotalInclTax() + $line->getTotalLineInclTax());
        }
        return $this;
    }

    public function getTaxData(): array
    {
        $data = []; // one line by tax rate
        foreach ($this->getInvoiceLines() as $line) {
            if (!array_key_exists((string)$line->getVatRate(), $data)) {
                $data[$line->getVatRate()] = ["taxRate" => $line->getVatRate(), "base" => 0, "tax" => 0];
            }
            $data[$line->getVatRate()]["base"] += $line->getTotalLineExclTax();
            $data[$line->getVatRate()]["tax"] += ($line->getTotalLineInclTax() - $line->getTotalLineExclTax());

        }
        return $data;
    }

    public function getAmountAlreadyPaid()
    {
        $total = 0;
        if($this->getLettering()) {
            $payments = $this->getLettering()->getPayments();
            if ($payments !== null) {
                foreach ($payments as $payment) {
                    $total += $payment->getAmountPaid();
                }
            }
        }

        return $total;
    }

    public function getBalance()
    {
        return round((float)$this->getTotalInclTax() - (float)$this->getAmountAlreadyPaid(), 10);
    }

    public function getFullAddress(): ?string
    {
        $fullAddress = $this->getCompany() . "\n";
        $fullAddress .= $this->getAddress() . "\n";
        if ($this->getAddress2()) {
            $fullAddress .= $this->getAddress2() . "\n";
        }
        $fullAddress .= $this->getPostalCode() . ' ' . $this->getCity();

        return $fullAddress;
    }

    public function getBillingAddress(): string
    {
        $addressCompta = ($this->getCustomer()->getBillingName() ?? $this->getCompany()) . "\n";
        $addressCompta .= $this->getCustomer()->getBillingDepartment() . "\n";
        if ($this->getCustomer()->getIsDifferentBillingAddress()) {
            $addressCompta .= ($this->getCustomer()->getBillingAddress1() ?? $this->getAddress()) . "\n";
            if ($this->getCustomer()->getBillingAddress2()) {
                $addressCompta .= $this->getCustomer()->getBillingAddress2() . "\n";
            }
            $addressCompta .= $this->getCustomer()->getBillingPostalCode() . ' ' . $this->getCustomer()->getBillingCity();
        } else {
            $addressCompta .= $this->getAddress() . "\n";
            if ($this->getAddress2()) {
                $addressCompta .= $this->getAddress2() . "\n";
            }
            $addressCompta .= $this->getPostalCode() . ' ' . $this->getCity();
        }

        return $addressCompta;
    }


    public function getTotalTax(): ?string
    {
        return number_format($this->getTotalInclTax() - $this->getTotalExclTax(), 2);
    }

    public function calcDueDate(): ?\DateTime
    {
        if ($this->invoiceDate && $this->getPaymentCondition()) {
            $dateValidation = clone $this->invoiceDate;
            $dateValidation->modify("+" . $this->getPaymentCondition()->getNumberOfDays() . "days");
            if ($this->getPaymentCondition()->getEndMonth()) {
                $dateValidation->modify('last day of this month');
            }

            return $dateValidation;
        }

        return null;
    }

    public function hasLineWithDiscount(): bool
    {
        /** @var InvoiceLineInterface $invoiceLine */
        foreach ($this->getInvoiceLines() as $invoiceLine) {
            if ($invoiceLine->getDiscount() > 0) {
                return true;
            }
        }
        return false;
    }

    public function initCustomer(CustomerInterface $customer): InvoiceInterface
    {
        return $this
            ->setCustomer($customer)
            ->setCompany($customer->getBillingName())
            ->setAddress($customer->getBillingAddress1())
            ->setAddress2($customer->getBillingAddress2())
            ->setPostalCode($customer->getBillingPostalCode())
            ->setCity($customer->getBillingCity())
            ->setCountryCode($customer->getBillingCountry())
            ->setPhone($customer->getPhone())
            ->setEmail($customer->getBillingEmail());
    }

    public function canBeDelete()
    {
        return $this->status === InvoiceInterface::STATUS_DRAFT;
    }

    public function getIdChorus()
    {
        return $this->idChorus;
    }

    public function setIdChorus($idChorus): self
    {
        $this->idChorus = $idChorus;

        return $this;
    }

    public function getCompany()
    {
        return $this->company;
    }

    public function setCompany($company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    public function setAddress2(?string $address2): self
    {
        $this->address2 = $address2;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(?string $countryCode): self
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }


    public function getInvoiceNumber(): ?string
    {
        return $this->invoiceNumber;
    }

    public function setInvoiceNumber(?string $invoiceNumber): self
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    public function getInvoiceDate(): ?\DateTimeInterface
    {
        return $this->invoiceDate;
    }

    public function setInvoiceDate(?\DateTimeInterface $invoiceDate): self
    {
        $this->invoiceDate = $invoiceDate;

        return $this;
    }

    public function getDueDate(): ?\DateTimeInterface
    {
        return $this->dueDate;
    }

    public function setDueDate(?\DateTimeInterface $dueDate): self
    {
        $this->dueDate = $dueDate;
        return $this;
    }

    public function getDiscount(): ?string
    {
        return $this->discount;
    }

    public function setDiscount(?string $discount): self
    {
        $this->discount = $discount;

        return $this;
    }

    public function getIsExportedAccounting(): ?bool
    {
        return $this->isExportedAccounting;
    }

    public function setIsExportedAccounting(?bool $isExportedAccounting): self
    {
        $this->isExportedAccounting = $isExportedAccounting;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getIsPaid(): ?bool
    {
        return $this->isPaid;
    }

    public function setIsPaid(?bool $isPaid): self
    {
        $this->isPaid = $isPaid;

        return $this;
    }

    public function getTotalExclTax(): ?string
    {
        return $this->totalExclTax;
    }

    public function setTotalExclTax(?string $totalExclTax): self
    {
        $this->totalExclTax = $totalExclTax;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getTotalInclTax(): ?string
    {
        return $this->totalInclTax;
    }

    public function setTotalInclTax(string $totalInclTax): self
    {
        $this->totalInclTax = $totalInclTax;

        return $this;
    }

    public function getCustomer(): ?CustomerInterface
    {
        return $this->customer;
    }

    public function setCustomer(?CustomerInterface $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getSeller(): ?SellerInterface
    {
        return $this->seller;
    }

    public function setSeller(?SellerInterface $seller): self
    {
        $this->seller = $seller;

        return $this;
    }

    public function getPaymentCondition(): ?PaymentConditionInterface
    {
        return $this->paymentCondition;
    }

    public function setPaymentCondition(?PaymentConditionInterface $paymentCondition): self
    {
        $this->paymentCondition = $paymentCondition;

        return $this;
    }

    public function getInvoiceLines(): Collection
    {
        return $this->invoiceLines;
    }

    public function addInvoiceLine(InvoiceLineInterface $invoiceLine): self
    {
        $this->invoiceLines->add($invoiceLine);
        $invoiceLine->setInvoice($this);
        return $this;
    }

    public function removeInvoiceLine(InvoiceLineInterface $invoiceLine): self
    {
        if ($this->invoiceLines->contains($invoiceLine)) {
            $this->invoiceLines->removeElement($invoiceLine);
            // set the owning side to null (unless already changed)
            if ($invoiceLine->getInvoice() === $this) {
                $invoiceLine->setInvoice(null);
            }
        }

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getReminder()
    {
        return $this->reminder;
    }

    public function setReminder(mixed $reminder)
    {
        $this->reminder = $reminder;
    }

    public function getCreditNote(): ?self
    {
        return $this->creditNote;
    }

    public function setCreditNote(InvoiceInterface $creditNote): self
    {
        $this->creditNote = $creditNote;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSequenceNumber()
    {
        return $this->sequenceNumber;
    }

    /**
     * @return InvoiceInterface
     */
    public function setSequenceNumber(mixed $sequenceNumber)
    {
        $this->sequenceNumber = $sequenceNumber;
        return $this;
    }


    public function getPaymentDate(): ?\DateTimeInterface
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(?\DateTimeInterface $paymentDate): self
    {
        $this->paymentDate = $paymentDate;

        return $this;
    }

    public function getLastReminderDate(): ?\DateTimeInterface
    {
        return $this->lastReminderDate;
    }

    public function setLastReminderDate(?\DateTimeInterface $lastReminderDate): self
    {
        $this->lastReminderDate = $lastReminderDate;

        return $this;
    }

    public function getLettering(): ?LetteringInterface
    {
        return $this->lettering;
    }

    public function setLettering(?LetteringInterface $lettering): self
    {
        $this->lettering = $lettering;

        return $this;
    }

    public function isLettered(): bool
    {
        return $this->lettering !== null;
    }
}
