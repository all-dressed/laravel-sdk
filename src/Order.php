<?php

namespace AllDressed;

use AllDressed\Builders\OrderBuilder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Order extends Base
{
    /**
     * Create a new order instance.
     */
    public function __construct(iterable $attributes = [])
    {
        if ($shipping = Arr::get($attributes, 'shipping')) {
            Arr::set($attributes, 'shipping', new Address($shipping));
        }

        if ($billing = Arr::get($attributes, 'billing')) {
            Arr::set($attributes, 'billing', new Address($billing));
        }

        if ($invoices = Arr::get($attributes, 'invoices')) {
            Arr::set(
                $attributes,
                'invoices',
                Collection::make($invoices)->mapInto(Invoice::class)
            );
        }

        if ($customers = Arr::get($attributes, 'customer')) {
            Arr::set($attributes, 'customer', Customer::make($customers));
        }

        parent::__construct($attributes);
    }

    /**
     * Create a new query builder.
     */
    public static function query(): OrderBuilder
    {
        return OrderBuilder::make();
    }
}
