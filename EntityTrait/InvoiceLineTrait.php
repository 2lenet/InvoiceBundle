<?php

namespace Lle\InvoiceBundle\EntityTrait;

use Doctrine\ORM\Mapping as ORM;
use Lle\InvoiceBundle\Model\InvoiceLineInterface;
use \Lle\InvoiceBundle\Model\VatRateInterface;
use \Lle\InvoiceBundle\Model\InvoiceInterface;
use \Lle\InvoiceBundle\Model\ProductInterface;


trait InvoiceLineTrait
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private $label;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $specificLabel;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private $quantity = 1;

    #[ORM\Column(type: 'string', length: 255)]
    private $code;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 2, nullable: true)]
    private $unitPrice;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private $discount = 0;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 2)]
    private $totalLineExclTax;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 2)]
    private $totalLineInclTax;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private $vatRate;

    #[ORM\ManyToOne(targetEntity: \Lle\InvoiceBundle\Model\ProductInterface::class, inversedBy: 'invoiceLines')]
    private $product;

    #[ORM\ManyToOne(targetEntity: \Lle\InvoiceBundle\Model\InvoiceInterface::class, inversedBy: 'invoiceLines')]
    private $invoice;

    #[ORM\ManyToOne(targetEntity: \Lle\InvoiceBundle\Model\VatRateInterface::class)]
    private $vat;

    #[ORM\Column(type: 'text', nullable: true)]
    private $comment;

    #[ORM\Column(type: 'date', nullable: true)]
    private $startDate;

    #[ORM\Column(type: 'date', nullable: true)]
    private $endDate;

    #[ORM\Column(type: 'boolean')]
    private $isManual = 0;

    public function __toString()
    {
        return $this->getProduct()->getLabel() ?? '';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function invoiceNotExportedAccounting()
    {
        return !$this->invoice->getIsExportedAccounting();
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getSpecificLabel(): ?string
    {
        return $this->specificLabel;
    }

    public function setSpecificLabel(?string $specificLabel): self
    {
        $this->specificLabel = $specificLabel;

        return $this;
    }

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function getQuantityAsInt(): ?string
    {
        return (int)$this->quantity;
    }

    public function setQuantity(string $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getUnitPrice(): ?string
    {
        return $this->unitPrice;
    }

    public function getRealUnitPrice(): ?string
    {
        if(!$this->getUnitPrice()){
            if ($product = $this->getProduct()){
                return $product->getUnitPrice();
            }
        }
        return $this->getUnitPrice();
    }

    public function setUnitPrice(string $unitPrice): self
    {
        $this->unitPrice = $unitPrice;

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

    public function getTotalLineExclTax(): ?string
    {
        return $this->totalLineExclTax;
    }

    public function setTotalLineExclTax(string $totalLineExclTax): self
    {
        $this->totalLineExclTax = $totalLineExclTax;

        return $this;
    }

    public function getTotalLineInclTax(): ?string
    {
        return $this->totalLineInclTax;
    }

    public function setTotalLineInclTax(string $totalLineInclTax): self
    {
        $this->totalLineInclTax = $totalLineInclTax;

        return $this;
    }

    public function getVatRate(): ?string
    {
        return $this->vatRate;
    }

    public function setVatRate(string $vatRate): self
    {
        $this->vatRate = $vatRate;

        return $this;
    }

    public function getProduct(): ?ProductInterface
    {
        return $this->product;
    }

    public function setProduct(?ProductInterface $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getInvoice(): ?InvoiceInterface
    {
        return $this->invoice;
    }

    public function setInvoice(?InvoiceInterface $invoice): self
    {
        $this->invoice = $invoice;

        return $this;
    }

    public function getVat(): ?VatRateInterface
    {
        return $this->vat;
    }

    public function setVat(?VatRateInterface $vatRate): self
    {
        $this->vat = $vatRate;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    public function setComment(mixed $comment)
    {
        $this->comment = $comment;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }


    public function getIsManual(): ?bool
    {
        return $this->isManual;
    }

    public function setIsManual(?bool $isManual): self
    {
        $this->isManual = $isManual;

        return $this;
    }

    public function updateTotalInvoiceLine(): InvoiceLineInterface
    {
        $this->setTotalLineExclTax((string) $this->calcTotalLineExclTax());
        $this->setTotalLineInclTax((string) $this->calcTotalLineInclTax());

        return $this;
    }

    public function calcTotalLineExclTax()
    {
        return $this->getQuantity()
            * $this->getRealUnitPrice()
            * (1 - $this->getDiscount() / 100);
    }

    public function calcTotalLineInclTax()
    {
        if ($this->getInvoice() && $this->getInvoice()->getCustomer() && $this->getInvoice()->getCustomer()->getVatNotApplicable()) {
            $vatRate = 0;
        } else {
            $vatRate =  $this->getProduct()->getVat() ? $this->getProduct()->getVat()->getRate() : 0;
        }

        return $this->getTotalLineExclTax()
            * (1 + $vatRate / 100);
    }

    public function getTotalDiscountLine()
    {
        return $this->getQuantity()
            * $this->getRealUnitPrice()
            * ($this->getDiscount() / 100);
    }

    public function initProduct(ProductInterface $product): self
    {
        $vatRate =  $product->getVat() ? $product->getVat()->getRate() : 0;

        return $this
            ->setProduct($product)
            ->setCode($this->getProduct()->getCode())
            ->setUnitPrice($this->getProduct()->getUnitPrice())
            ->setVatRate($vatRate)
            ->setVat($this->getProduct()->getVat())
            ->setTotalLineExclTax($this->calcTotalLineExclTax())
            ->setTotalLineInclTax($this->calcTotalLineInclTax())
            ;
    }
}
