<?php

namespace AllDressed;

use AllDressed\Concerns\Makeable;
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
        'line_1',
        'line_2',
        'city',
        'state',
        'postcode',
        'country',
    ];

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
     * Set the city of the address
     *
     * @return static
     */
    public function setCity(string $city): static
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Set the country of the address
     *
     * @return static
     */
    public function setCountry(string $country): static
    {
        $this->country = $country;

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
     * Set the postcode of the address
     *
     * @return static
     */
    public function setPostcode(string $postcode): static
    {
        $this->postcode = $postcode;

        return $this;
    }

    /**
     * Set the state of the address
     *
     * @return static
     */
    public function setState(string $state): static
    {
        $this->state = $state;

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
            "{$prefix}address_line_1" => $this->line_1,
            "{$prefix}address_line_2" => $this->line_2,
            "{$prefix}city" => $this->city,
            "{$prefix}state" => $this->state,
            "{$prefix}postcode" => $this->postcode,
            "{$prefix}country" => $this->country,
        ];
    }
}
