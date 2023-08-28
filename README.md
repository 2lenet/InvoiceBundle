# InvoiceBundle v2

This bundle is useful to manage invoice in your project. 

## Installation

To install the bundle: 

```
composer require 2le/invoice-bundle
```

## Configuration

To configure the bundle, in the doctrine service configuration file ("config/packages/doctrine.yaml"), you have to define which entity will get the role of customer. 

For exemple, if you want to use your `User` entity, you will add this block at the end of file : 

```yaml
config/packages/doctrine.yaml:
    ...
    orm:
        ...
        resolve_target_entities:
            Lle\InvoiceBundle\Model\CustomerInterface: App\Entity\User
            Lle\InvoiceBundle\Model\ProductInterface: App\Entity\Product
            Lle\InvoiceBundle\Model\CustomerInterface: App\Entity\Guest
            Lle\InvoiceBundle\Model\ProductInterface: App\Entity\InvoiceProduct
            Lle\InvoiceBundle\Model\SellerInterface: App\Entity\Establishment
            Lle\InvoiceBundle\Model\InvoiceInterface: App\Entity\Invoice
            Lle\InvoiceBundle\Model\InvoiceLineInterface: App\Entity\InvoiceLine
            Lle\InvoiceBundle\Model\PaymentInterface: App\Entity\Payment
            Lle\InvoiceBundle\Model\PaymentConditionInterface: App\Entity\PaymentCondition
            Lle\InvoiceBundle\Model\PaymentTypeInterface: App\Entity\PaymentType
            Lle\InvoiceBundle\Model\VatRateInterface: App\Entity\VatRate

```

Then, when the bundle expect a CustomerInterface you can pass an user. Once this listener is configured, Doctrine will know how to replace the customer interface. And that's the same for the Product entity.

> To get more info about relationships with abstract classes and interfaces, you can check official Symfony Documentation [here](https://symfony.com/doc/current/doctrine/resolve_target_entity.html)

Do not forget to implement methods on your entity using bundle traits and add your custom needs.

```php
class Invoice implements InvoiceInterface
{
    use InvoiceTrait;
    
    ...
}
```


#### PDF Parameter

Use PdfGenerator
~~To export invoice in PDF, you have to define paramater in the file "config/packages/lle_invoice.yaml". For example, you can define parameter like that :~~

```yaml
lle_invoice:
  # Relative path to image (png or jpg) from root directory
  logo: 'public/image/image.jpg'
  header: 'header \n address'
  footer: 'footer \n info'
```

~~logo: Path of image to put on Invoice header~~

~~The other fields concern the company who made fixtures.~~


## Usage

In this bundle, there are several services : InvoiceManager, InvoiceLineManager, InvoiceExporter, InvoicePDF. You can use all of them with Dependency Injection. You can use EasyAdmin or EasyAdminPlus to manage this entity. 

### Invoice Manager

InvoiceManager is a service that let you generate a draft invoice or validate an invoice. 

#### Generate a draft invoice

A draft invoice is an invoice with pre-filled fields. 

Example: 

```php
    public function index(InvoiceManager $invoiceManager): Response
    {
        ...
        // To get a draft Invoice
        $invoiceManager->generate();    
        ...  
    }

```

But if you want to get more pre-filled fields, you can pass an entity that **implements** CustomerInterface : 

```php
    public function index(InvoiceManager $invoiceManager): Response
    {
        ...
        // Entity User implements CustomerInterface in this example
        $invoiceManager->generate($user);    
        ...  
    }

```

#### Validate an invoice

If you want to validate an invoice, you can use the InvoiceManager. Validate an Invoice set the invoice number, puts current date and set status to "Validate". 

Example: 

```php
    public function index(InvoiceManager $invoiceManager): Response
    {
        ...
        $invoiceManager->validate($invoice);    
        ...  
    }

```

Then, you can persist your entity or do whatever you want. 

### Invoice Exporter

You can export one or more invoice in csv format. 

Example:

```php
    public function index(InvoiceExporter $invoiceExporter, Invoice $invoice): Response
    {
        ...
        $invoiceExporter->export([$invoice]);    
        ...  
    }
```

You can choose the filename with: export([$invoice1, $invoice2], 'invoice_export.csv');




