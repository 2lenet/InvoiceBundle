<?php


namespace Lle\InvoiceBundle\EntityTrait;

use Doctrine\ORM\Mapping as ORM;

trait PaymentTypeTrait
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $label;


    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $code;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $zugferd_code;

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


    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function getZugferdCode(): ?string
    {
        return $this->zugferd_code;
    }

    public function setZugferdCode(string $zugferd_code): self
    {
        $this->zugferd_code = $zugferd_code;
        return $this;
    }
}
