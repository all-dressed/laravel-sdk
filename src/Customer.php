<?php

namespace AllDressed;

use AllDressed\Builders\CustomerBuilder;
use AllDressed\Exceptions\MissingBillingAddressException;
use Illuminate\Support\Collection;

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
     * Retrieve the invoices of the customer.
     *
     * @param  int  $page
     * @return \Illuminate\Support\Collection<int, \AllDressed\Invoice>
     */
    public function getInvoices(int $page = 1): Collection
    {
        return Invoice::query()->forCustomer($this)->setPage($page)->get();
    }

    /**
     * Retrieve the payment methods of the customer.
     *
     * @return \Illuminate\Support\Collection<int, \AllDressed\PaymentMethod>
     */
    public function getPaymentMethods(): Collection
    {
        return PaymentMethod::query()->forCustomer($this)->get();
    }

    /**
     * Retrieve the subscriptions of the customer.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getSubscriptions(): Collection
    {
        return Subscription::query()->forCustomer($this)->get();
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
