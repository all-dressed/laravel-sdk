<?php

namespace AllDressed\Builders\Concerns;

use AllDressed\Address;
use AllDressed\Constants\AddressType;

trait HasShippingAddress
{
    /**
     * Set the shipping address of the request.
     */
    public function setShippingAddress(Address $address): static
    {
        if ($address->hasLine2()) {
            $this->setShippingAddressLine2($address->line_2);
        }

        if ($address->hasCompany()) {
            $this->setShippingCompany($address->company);
        }

        return $this
            ->setShippingAddressType($address->type)
            ->setShippingAddressLine1($address->line_1)
            ->setShippingCity($address->city)
            ->setShippingState($address->state)
            ->setShippingPostcode($address->postcode)
            ->setShippingCountry($address->country);
    }

    /**
     * Set the shipping address line 1 of the request.
     */
    public function setShippingAddressLine1(string $address): static
    {
        return $this->withOption('shipping_address_line_1', $address);
    }

    /**
     * Set the shipping address line 2 of the request.
     */
    public function setShippingAddressLine2(string $suite): static
    {
        return $this->withOption('shipping_address_line_2', $suite);
    }

    /**
     * Set the shipping address type of the request.
     */
    public function setShippingAddressType(AddressType $type): static
    {
        return $this->withOption('shipping_address_type', $type);
    }

    /**
     * Set the shipping city of the request.
     */
    public function setShippingCity(string $city): static
    {
        return $this->withOption('shipping_city', $city);
    }

    /**
     * Set the shipping company of the request.
     */
    public function setShippingCompany(string $company): static
    {
        return $this->withOption('shipping_company', $company);
    }

    /**
     * Set the shipping country of the request.
     */
    public function setShippingCountry(string $country): static
    {
        return $this->withOption('shipping_country', $country);
    }

    /**
     * Set the shipping postcode of the request.
     */
    public function setShippingPostcode(string $postcode): static
    {
        return $this->withOption('shipping_postcode', $postcode);
    }

    /**
     * Set the shipping state of the request.
     */
    public function setShippingState(string $state): static
    {
        return $this->withOption('shipping_state', $state);
    }
}
