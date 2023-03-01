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
}
