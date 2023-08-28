<?php

namespace Lle\InvoiceBundle\EntityTrait;

use Doctrine\ORM\Mapping as ORM;

trait PaymentConditionTrait
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $label;

    #[ORM\Column(type: 'integer')]
    private $numberOfDays;

    #[ORM\Column(type: 'boolean')]
    private $endMonth;

    public function __toString(): string
    {
        return $this->getLabel() ?? '';
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNumberOfDays(): ?int
    {
        return $this->numberOfDays;
    }

    public function setNumberOfDays(int $numberOfDays): self
    {
        $this->numberOfDays = $numberOfDays;

        return $this;
    }

    public function getEndMonth(): ?bool
    {
        return $this->endMonth;
    }

    public function setEndMonth(bool $endMonth): self
    {
        $this->endMonth = $endMonth;

        return $this;
    }
}
