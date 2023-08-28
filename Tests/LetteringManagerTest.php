<?php

namespace Lle\InvoiceBundle\Tests;


use Doctrine\ORM\EntityManagerInterface;
use Lle\InvoiceBundle\Model\InvoiceInterface;
use Lle\InvoiceBundle\Model\LetteringInterface;
use Lle\InvoiceBundle\Model\PaymentInterface;
use Lle\InvoiceBundle\Service\LetteringManager;
use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\TestCase;
use \Doctrine\Persistence\Mapping\ClassMetadata;

class LetteringManagerTest extends TestCase
{
    private \Lle\InvoiceBundle\Service\LetteringManager $letteringManager;

    public function setUp(): void
    {
        $abstractClassMetadataFactoryMock = $this->createMock(ClassMetadata::class);
        $abstractClassMetadataFactoryMock->method('getName')->willReturn(LetteringInterface::class);
        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getClassMetadata')->with(LetteringInterface::class)->willReturn($abstractClassMetadataFactoryMock);
        $this->letteringManager = new LetteringManager($em);
    }

    public function testLettering(): void
    {
        $this->setUp();

        $payments = $this->getPayments();
        $invoices = $this->getInvoices();

        $lettering = $this->letteringManager->letter($invoices, $payments);

        $this->assertNotNull($lettering);
    }

    public function testNoLettering(): void
    {
        $this->setUp();

        $payments = $this->getPayments();
        $invoices = $this->getInvoices();

        array_pop($invoices);

        $lettering = $this->letteringManager->letter($invoices, $payments);

        $this->assertNull($lettering);
    }

    public function testLetteringOnlyInvoices(): void
    {
        $this->setUp();

        $invoices = $this->getInvoices();
        $payments = [];

        $invoice = $this->createMock(InvoiceInterface::class);
        $invoice->method('getTotalInclTax')->will($this->returnValue("-400"));

        array_push($invoices, $invoice);

        $lettering = $this->letteringManager->letter($invoices, $payments);

        $this->assertNotNull($lettering);
    }
    
    public function getPayments(): array
    {
        $object1 = $this->createMock(PaymentInterface::class);
        $object1->method('getAmountPaid')->will($this->returnValue(200));
        $object2 = $this->createMock(PaymentInterface::class);
        $object2->method('getAmountPaid')->will($this->returnValue(50));
        $object3 = $this->createMock(PaymentInterface::class);
        $object3->method('getAmountPaid')->will($this->returnValue(150));

        return [$object1, $object2, $object3];
    }
    
    public function getInvoices(): array
    {
        $object1 = $this->createMock(InvoiceInterface::class);
        $object1->method('getTotalInclTax')->will($this->returnValue("50"));
        $object2 = $this->createMock(InvoiceInterface::class);
        $object2->method('getTotalInclTax')->will($this->returnValue("150"));
        $object3 = $this->createMock(InvoiceInterface::class);
        $object3->method('getTotalInclTax')->will($this->returnValue("200"));

        return [$object1, $object2, $object3];
    }

}