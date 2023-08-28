<?php


namespace Lle\InvoiceBundle\Service;


use Doctrine\ORM\EntityManagerInterface;
use Lle\InvoiceBundle\EntityTrait\InvoiceLineTrait;
use Lle\InvoiceBundle\EntityTrait\InvoiceTrait;
use Lle\InvoiceBundle\Model\CustomerInterface;
use Lle\InvoiceBundle\Model\InvoiceInterface;
use Lle\InvoiceBundle\Model\InvoiceLineInterface;
use Lle\InvoiceBundle\Model\ProductInterface;
use Lle\InvoiceBundle\NumberStrategy\NumberStrategyInterface;

class InvoiceManager
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly NumberStrategyInterface $numberStrategy,
    ) {
    }

    public function generate(?CustomerInterface $customer): InvoiceInterface
    {
        $invoiceClass = $this->em->getClassMetadata(InvoiceInterface::class)->getName();
        $sampleInvoice = new $invoiceClass();

        if ($customer){
            $sampleInvoice
                ->initCustomer($customer)
                ->setName($customer->getName())
                ->setFirstName($customer->getFirstName())
            ;
        }
        $sampleInvoice
            ->setIsPaid(false)
            ->setIsExportedAccounting(false)
        ;
        return $sampleInvoice;
    }

    public function draftInvoice(InvoiceInterface $invoice): InvoiceInterface
    {
        return $invoice
            ->setStatus(InvoiceInterface::STATUS_DRAFT)
            ->setDiscount("0")
            ->setIsExportedAccounting(false)
            ->setIsPaid(false)
            ->setTotalInclTax("0")
            ->setTotalExclTax("0")
        ;
    }

    public function validation(InvoiceInterface $invoice): InvoiceInterface
    {
        $invoice
            ->setStatus(InvoiceInterface::STATUS_VALIDATE)// Workflow or not workflow
            ->initCustomer($invoice->getCustomer())
        ;
        
        if($invoice->getCreditNote()) {
            $invoice->setStatus(InvoiceInterface::STATUS_BALANCE);
            $invoice->setIsPaid(true);
            $invoice->setPaymentDate( (new \DateTime()));
            $origin = $invoice->getCreditNote();
            $origin->setStatus(InvoiceInterface::STATUS_BALANCE);
            $origin->setIsPaid(true);
            $origin->setPaymentDate( (new \DateTime()));
        }
        
        if($invoice->getInvoiceDate() === null) {
             $invoice->setInvoiceDate(( new \DateTime()));
        }
        $invoice->setDueDate($invoice->calcDueDate());
        
        if(!$invoice->getSequenceNumber()){
            $this->numberStrategy->setNumber($invoice);
        }
        
        if($invoice->getType() != InvoiceInterface::TYPE_CREDIT){
            /** @var InvoiceLineInterface $invoiceLine */
            foreach($invoice->getInvoiceLines() as $invoiceLine) {
                if(!$invoiceLine->getIsManual()) {
                    $invoiceLine->setUnitPrice((string)$invoiceLine->getProduct()->getUnitPrice());
                    $invoiceLine->updateTotalInvoiceLine();
                }
            }
        }
        $invoice->updateTotalInvoice();
        return $invoice;
    }

    public function cancelValidation(InvoiceInterface $invoice): InvoiceInterface
    {
        return $invoice
            ->setStatus(InvoiceInterface::STATUS_DRAFT);
    }

    public function createCreditFromInvoice(InvoiceInterface $invoice): InvoiceInterface
    {
        $invoiceClass = $this->em->getClassMetadata(InvoiceInterface::class)->getName();
        /** @var InvoiceInterface $avoir */
        $avoir = new $invoiceClass();
        $avoir
            ->setType(InvoiceInterface::TYPE_CREDIT)
            ->setStatus(InvoiceInterface::STATUS_DRAFT)
            ->setTotalExclTax((string)((float)$invoice->getTotalExclTax() * -1))
            ->setTotalInclTax((string)((float)$invoice->getTotalInclTax() * -1))
            ->setSeller($invoice->getSeller())
            ->setName($invoice->getName())
            ->setFirstName($invoice->getFirstName())
            ->setDiscount($invoice->getDiscount())
            ->setIsPaid($invoice->getIsPaid())
            ->setCustomer($invoice->getCustomer())
            ->setComment($invoice->getComment())
            ->setAddress($invoice->getAddress())
            ->setAddress2($invoice->getAddress2())
            ->setCity($invoice->getCity())
            ->setCountryCode($invoice->getCountryCode())
            ->setPhone($invoice->getPhone())
            ->setPostalCode($invoice->getPostalCode())
            ->setEmail($invoice->getEmail())
            ->setPaymentCondition($invoice->getPaymentCondition())
            ->setIsExportedAccounting(false)
        ;

        /** @var InvoiceLineInterface $invoiceLine */
        foreach ($invoice->getInvoiceLines() as $invoiceLine){
            $invoiceLineClass = $this->em->getClassMetadata(InvoiceLineInterface::class)->getName();
            /** @var InvoiceLineInterface $invoiceLine2 */
            $invoiceLine2 = new $invoiceLineClass();

            $invoiceLine2
                ->setUnitPrice((string)$invoiceLine->getUnitPrice())
                ->setTotalLineExclTax((string)((float)$invoiceLine->getTotalLineExclTax() * -1))
                ->setTotalLineInclTax((string)((float)$invoiceLine->getTotalLineInclTax() * -1))
                ->setCode($invoiceLine->getCode())
                ->setLabel($invoiceLine->getLabel())
                ->setSpecificLabel($invoiceLine->getSpecificLabel())
                ->setQuantity("-".$invoiceLine->getQuantity())
                ->setProduct($invoiceLine->getProduct())
                ->setDiscount($invoiceLine->getDiscount())
                ->setVatRate($invoiceLine->getVatRate())
                ->setVat($invoiceLine->getVat())
                ->setStartDate($invoiceLine->getStartDate())
                ->setEndDate($invoiceLine->getEndDate())
            ;
            $avoir->addInvoiceLine($invoiceLine2);
        }
        $invoice->setCreditNote($avoir);

        return $avoir;
    }

    /**
     * Automatically create an invoice from a customer and a product (optional). Without product, an empty invoice will be generated, it is possible to specify the quantity on the invoice line and to specify a label.
     * @param ProductInterface|null $product
     * @param string|null $specificLabel
     * @return InvoiceInterface
     */
    public function createInvoiceFromProduct(CustomerInterface $customer, ProductInterface $product = null, int $quantity = 1, string $specificLabel = null)
    {
        $invoiceClass = $this->em->getClassMetadata(InvoiceInterface::class)->getName();
        $invoice = new $invoiceClass();
        $invoice
            ->initCustomer($customer)
            ->setStatus(InvoiceInterface::STATUS_DRAFT)
            ->setDiscount("0")
            ->setIsExportedAccounting(false)
            ->setIsPaid(false)
            ->setPaymentCondition($product ? $product->getPaymentCondition() : null)
        ;
        if ($product) {
            $invoiceLineClass = $this->em->getClassMetadata(InvoiceLineInterface::class)->getName();
            /** @var InvoiceLineInterface $invoiceLine */
            $invoiceLine = new $invoiceLineClass();
            $invoice->addInvoiceLine($invoiceLine);
            $invoiceLine
                ->initProduct($product)
                ->setLabel($specificLabel ?? $product->getLabel())
                ->setDiscount("0")
                ->setQuantity((string)$quantity)
            ;
        }
        return $invoice->updateTotalInvoice();
    }
}
