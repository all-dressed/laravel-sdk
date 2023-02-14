<?php

namespace AllDressed;

use Illuminate\Support\Fluent;

class Address extends Fluent
{
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
     * Create a new card instance.
     *
     * @return static
     */
    public static function make(...$args): static
    {
        return new static(...$args);
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
}
