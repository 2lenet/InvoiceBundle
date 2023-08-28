<?php


namespace Lle\InvoiceBundle\Model;


interface SellerInterface
{
    public function getCompany();

    public function getName();

    public function getFirstName();

    public function getAddress(): ?string;

    public function getAddress2(): ?string;

    public function getPostalCode(): ?string;

    public function getCity(): ?string;

    public function getCountryCode(): ?string;

    public function getPhone(): ?string;

    public function getEmail(): ?string;

    public function getCurrencyCode(): ?string;

}
