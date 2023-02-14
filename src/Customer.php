<?php

namespace AllDressed;

use AllDressed\Builders\CustomerBuilder;
use AllDressed\Exceptions\MissingBillingAddressException;

class Customer extends Base
{
    /**
     * Add a payment method to the customer's profile.
     *
     * @param  \AllDressed\PaymentGateway  $gateway
     * @param  \AllDressed\Card  $card
     * @param  \AllDressed\Address|null  $address
     * @return \AllDressed\PaymentMethod
     */
    public function addPaymentMethod(PaymentGateway $gateway, Card $card, Address $address = null): PaymentMethod
    {
        throw_unless(
            $address ??= $card->address,
            MissingBillingAddressException::class
        );

        return PaymentMethod::query()
            ->forGateway($gateway)
            ->forCustomer($this)
            ->setBillingAddress($address)
            ->create($card);
    }

    /**
     * Create a new query builder.
     *
     * @return \AllDressed\Builders\CustomerBuilder
     */
    public static function query(): CustomerBuilder
    {
        return CustomerBuilder::make();
    }

    /**
     * Send the request to create or update the customer.
     *
     * @return static
     */
    public function save(): static
    {
        if ($this->id === null) {
            return static::query()->create($this->getAttributes());
        }

        return $this->update();
    }
}
