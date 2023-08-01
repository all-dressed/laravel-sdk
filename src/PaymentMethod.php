<?php

namespace AllDressed;

use AllDressed\Builders\PaymentMethodBuilder;
use Illuminate\Support\Arr;

class PaymentMethod extends Base
{
    /**
     * Create a new payment method instance.
     *
     * @param  iterable<TKey, TValue>  $attributes
     * @return void
     */
    public function __construct($attributes = [])
    {
        if ($address = Arr::get($attributes, 'address')) {
            $attributes['address'] = new Address($address);
        }

        parent::__construct($attributes);
    }

    /**
     * Delete the payment method.
     *
     * @return static
     */
    public function delete(): static
    {
        static::query()->for($this)->forCustomer($this->customer)->delete();

        return $this;
    }

    /**
     * Create a new query builder.
     *
     * @return \AllDressed\Builders\PaymentMethodBuilder
     */
    public static function query(): PaymentMethodBuilder
    {
        return PaymentMethodBuilder::make();
    }

    /**
     * Set the default payment method and/or the subscription payment methods.
     *
     * @param  bool  $update
     * @return static
     */
    public function setAsDefault($update = true): static
    {
            static::query()
                ->for($this)
                ->forCustomer($this->customer)
                ->setAsDefault([
                    'subscriptions' => $update,
                ]);

        return $this;
    }

    /**
     * Update the payment method.
     *
     * @param  int  $month
     * @param  int  $year
     * @param  \AllDressed\Address  $address
     * @return static
     */
    public function update(int $month, int $year, Address $address): static
    {
        static::query()
            ->for($this)
            ->forCustomer($this->customer)
            ->update(array_merge(array_filter($address->toPayload()), [
                'month' => $month,
                'year' => $year,
            ]));

        return $this;
    }
}
