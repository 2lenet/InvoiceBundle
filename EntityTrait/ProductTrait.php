<?php

namespace Lle\InvoiceBundle\EntityTrait;

use Doctrine\ORM\Mapping as ORM;
use Lle\InvoiceBundle\Model\PaymentConditionInterface;
use \Lle\InvoiceBundle\Model\VatRateInterface as VatRateInterface;


trait ProductTrait
{
    #[ORM\Column(type: 'string', length: 255)]
    private $label;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 2)]
    private $unitPrice;

    #[ORM\Column(type: 'string', length: 255)]
    private $accountingNumber;

    #[ORM\ManyToOne(targetEntity: VatRateInterface::class)]
    private $vat;

    #[ORM\Column(type: 'string', length: 255)]
    private $code;

    #[ORM\ManyToOne(targetEntity: \Lle\InvoiceBundle\Model\PaymentConditionInterface::class)]
    private $paymentCondition;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $analyticCode;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getUnitPrice(): ?float
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(float $unitPrice): self
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    public function getAccountingNumber(): ?string
    {
        return $this->accountingNumber;
    }

    public function setAccountingNumber(string $accountingNumber): self
    {
        $this->accountingNumber = $accountingNumber;

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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getPaymentCondition(): ?PaymentConditionInterface
    {
        return $this->paymentCondition;
    }

    public function setPaymentCondition(PaymentConditionInterface $paymentCondition): self
    {
        $this->paymentCondition = $paymentCondition;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAnalyticCode()
    {
        return $this->analyticCode;
    }

    public function setAnalyticCode(mixed $analyticCode): void
    {
        $this->analyticCode = $analyticCode;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription(mixed $description): void
    {
        $this->description = $description;
    }
}
