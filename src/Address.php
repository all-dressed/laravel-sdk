<?php

namespace AllDressed;

use AllDressed\Concerns\Makeable;
use AllDressed\Constants\AddressType;
use Illuminate\Support\Fluent;

class Address extends Fluent
{
    use Makeable;

    /**
     * The attributes that are mass assignable.
     *
     * * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'type',
        'line_1',
        'line_2',
        'company',
        'city',
        'state',
        'postcode',
        'country',
    ];

    /**
     * Check if the address has a company name.
     *
     * @return bool
     */
    public function hasCompany(): bool
    {
        return $this->company !== null;
    }

    /**
     * Check if the address has a line 2.
     *
     * @return bool
     */
    public function hasLine2(): bool
    {
        return $this->line_2 !== null;
    }

    /**
     * Set the city of the address.
     *
     * @param  string  $city
     * @return static
     */
    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Set the company name of the address.
     *
     * @param  string  $company
     * @return static
     */
    public function setCompany(string $company): static
    {
        $this->type = AddressType::BUSINESS;

        $this->company = $company;

        return $this;
    }

    /**
     * Set the country of the address.
     *
     * @param  string  $country
     * @return static
     */
    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Set the first name of the address.
     *
     * @param  string  $name
     * @return static
     */
    public function setFirstName(string $name): static
    {
        $this->first_name = $name;

        return $this;
    }

    /**
     * Set the last name of the address.
     *
     * @param  string  $name
     * @return static
     */
    public function setLastName(string $name): static
    {
        $this->last_name = $name;

        return $this;
    }

    /**
     * Set the line 1 of the address.
     *
     * @param  string  $address
     * @return static
     */
    public function setLine1(string $address): static
    {
        $this->line_1 = $address;

        return $this;
    }

    /**
     * Set the line 2 of the address.
     *
     * @param  string  $address
     * @return static
     */
    public function setLine2(string $address): static
    {
        $this->line_2 = $address;

        return $this;
    }

    /**
     * Set the phone of the address.
     *
     * @param  string  $phone
     * @return static
     */
    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Set the postcode of the address.
     *
     * @param  string  $postcode
     * @return static
     */
    public function setPostcode(string $postcode): static
    {
        $this->postcode = $postcode;

        return $this;
    }

    /**
     * Set the state of the address.
     *
     * @param  string  $state
     * @return static
     */
    public function setState(string $state): static
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Set the type of the address.
     *
     * @param  \AllDressed\Constants\AddressType  $type
     * @return static
     */
    public function setType(AddressType $type): static
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Convert the address to a payload for a request.
     *
     * @param  string|null  $prefix
     * @return array
     */
    public function toPayload(string $prefix = null): array
    {
        return [
            "{$prefix}first_name" => $this->first_name,
            "{$prefix}last_name" => $this->last_name,
            "{$prefix}phone" => $this->phone,
            "{$prefix}address_type" => $this->type,
            "{$prefix}address_line_1" => $this->line_1,
            "{$prefix}address_line_2" => $this->line_2,
            "{$prefix}company" => $this->company,
            "{$prefix}city" => $this->city,
            "{$prefix}state" => $this->state,
            "{$prefix}postcode" => $this->postcode,
            "{$prefix}country" => $this->country,
        ];
    }
}
