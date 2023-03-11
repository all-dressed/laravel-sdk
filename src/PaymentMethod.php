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
     * Create a new query builder.
     *
     * @return \AllDressed\Builders\PaymentMethodBuilder
     */
    public static function query(): PaymentMethodBuilder
    {
        return PaymentMethodBuilder::make();
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
